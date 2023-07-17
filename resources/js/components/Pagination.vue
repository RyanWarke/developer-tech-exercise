<template>
  <div v-if="totalPages > 0" class="flex justify-between items-center py-6 mt-6 border-t-2 border-gray-300">
    <div class="flex sm:hidden flex-1 justify-between">
      <button type="button" :disabled="page === 1" class="inline-flex relative items-center py-2 px-4 text-sm font-medium text-gray-700 hover:text-gray-500 bg-white rounded-md border border-gray-300" @click="previousPage">
        Previous
      </button>
      <button type="button" :disabled="page === totalPages" class="inline-flex relative items-center py-2 px-4 ml-3 text-sm font-medium text-gray-700 hover:text-gray-500 bg-white rounded-md border border-gray-300" @click="nextPage">
        Next
      </button>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:justify-between sm:items-center">
      <div>
        <p class="m-0 text-sm text-blue-dark">
          Showing
          <span class="font-bold">{{ from }}</span>
          to
          <span class="font-bold">{{ to }}</span>
          of
          <span class="font-bold">{{ totalItems }}</span>
          {{ $filters.pluralize(text, totalItems, false) }}
        </p>
      </div>
      <div>
        <nav v-if="totalPages > 1" class="inline-flex relative z-0" aria-label="Pagination">
          <button type="button" :disabled="page === 1" class="inline-flex relative items-center py-2 px-2 mx-1 text-sm font-medium hover:text-white rounded-md text-blue-dark hover:bg-blue-500" @click="previousPage">
            <span class="sr-only">Previous</span>
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </button>

          <button
            v-for="(button, index) in buttons"
            :key="index"
            :class="button.active ? 'font-black bg-blue-light text-blue-500' : 'font-medium text-black'"
            class="inline-flex relative items-center py-2 px-4 mx-1 text-sm font-medium hover:text-white rounded-md hover:bg-blue-500"
            @click="$emit('page-change', button.page)"
          >
            {{ button.html }}
          </button>

          <button type="button" :disabled="page === totalPages" class="inline-flex relative items-center py-2 px-2 mx-1 text-sm font-medium hover:text-white rounded-md text-blue-dark hover:bg-blue-500" @click="nextPage">
            <span class="sr-only">Next</span>
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
import pluralize from '@/filters/pluralize';

export default {
  props: {
    from: {
      type: Number,
      default: 0
    },
    to: {
      type: Number,
      default: 0
    },
    page: {
      type: Number,
      default: 0,
      validator: (page) => page >= 0
    },
    itemsPerPage: {
      type: Number,
      default: 10,
      validator: (itemsPerPage) => itemsPerPage > 0
    },
    maxVisiblePages: {
      type: Number,
      default: 5,
      validator: (maxVisiblePages) => maxVisiblePages > 0
    },
    totalItems: {
      type: Number,
      required: true,
      validator: (totalItems) => totalItems >= 0
    },
    loading: {
      type: Boolean,
      default: false
    },
    text: {
      type: String,
      required: false,
      default: 'item'
    }
  },
  computed: {
    totalPages() {
      if (this.itemsPerPage === 0) return 0;
      return Math.ceil(this.totalItems / this.itemsPerPage);
    },
    pages() {
      const { filteredPages } = this;
      const pages = filteredPages
        ? [filteredPages[0] - 1 === 1 ? 1 : '...', ...filteredPages, filteredPages[filteredPages.length - 1] + 1 === this.totalPages - 2 ? this.totalPages - 2 : '...']
        : [...Array(this.totalPages - 2).keys()].map((page) => page + 1);

      return [
        1,
        ...pages,
        this.totalPages - 1,
        this.totalPages
      ];
    },
    filteredPages() {
      const diff = this.maxVisiblePages / 2;
      const toFilterPages = [...Array(this.totalPages).keys()].slice(2, -2);

      if (toFilterPages.length > this.maxVisiblePages) {
        const diffFirst = this.page - toFilterPages[0];
        const diffLast = this.page - toFilterPages[toFilterPages.length - 1];

        if (diffFirst < diff) {
          return toFilterPages.slice(0, this.maxVisiblePages);
        } if (diffLast >= -diff) {
          return toFilterPages.slice(-this.maxVisiblePages);
        }
        return toFilterPages.filter((page) => {
          const diffPage = this.page - page;

          return diffPage < 0 ? Math.abs(diffPage) <= diff : diffPage < diff;
        });
      }

      return null;
    },
    buttons() {
      const pagesMinusDuplicates = [...new Set(this.pages)]; // Remove dupicates
      return pagesMinusDuplicates.map((page, key) => ({
        page,
        active: page === this.page,
        disabled: this.disabled(page, key),
        html: this.html(page, key),
        title: this.title(key),
        loading: this.loading && page === this.page
      }));
    }
  },
  methods: {
    pluralize,
    previousPage() {
      this.$emit('page-change', this.page - 1);
    },
    nextPage() {
      this.$emit('page-change', this.page + 1);
    },
    html(page) {
      if (page === '...') {
        return page;
      }

      return `${page}`;
    },
    disabled(page, key) {
      return key === 0 && this.page === 0 || key === this.pages.length - 1 && this.page === this.totalPages - 1 || page === '...';
    },
    title(key) {
      if (key === 0) {
        return 'previous';
      }

      if (key === this.pages.length - 1) {
        return 'next';
      }

      return '';
    }
  }
};
</script>
