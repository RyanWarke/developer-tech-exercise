<template>
  <Loading v-if="loading" />
  <div v-else class="my-8 mx-10">
    <div class="w-full bg-[#FFEAE3] p-12 rounded-3xl">
      <h2 class="text-4xl font-black">Employee Directory</h2>
      <table class="w-full mt-4 border-separate border-spacing-y-3">
        <thead>
          <tr>
            <th class="py-2 px-8 text-left font-light text-gray-500">Employee</th>
            <th class="py-2 px-8 text-right font-light text-gray-500">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(employee, employeeIndex) in employees" :key="employeeIndex" class="py-2 my-2">
            <td class="py-4 px-8 rounded-l-3xl bg-white">
              <div class="flex items-center gap-6">
                <div class="rounded-3xl bg-blue-500 h-24 w-24 flex items-center justify-center">
                  <h2 class="text-2xl text-white font-black tracking-widest">{{ `${employee.forename[0]}${employee.surname[0]}` }}</h2>
                </div>
                <h2 class="text-lg font-bold">{{ employee.title }} {{ employee.forename }} {{ employee.surname }}</h2>
              </div>
            </td>
            <td class="px-8 py-4 rounded-r-3xl bg-white text-right">
              <router-link :to="`/class/${employee.id}`" class="hover:underline">View Classes</router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <Pagination
        :from="from"
        :to="to"
        :total-items="total"
        :page="current_page"
        :items-per-page="10"
        text="Employees"
        @page-change="getEmployees"
      />
  </div>
</template>

<script>
import Pagination from '@/components/Pagination.vue'
import Loading from '@/components/Loading.vue';

export default {
  components: {
    Pagination,
    Loading
  },
  data() {
    return {
      loading: false,
      to: 0,
      from: 0,
      current_page: 1,
      total: 0,
      employees: [],
    }
  },
  mounted() {
    this.getEmployees(1);
  },
  methods: {
    getEmployees(page = 1) {
      this.loading = true;
      this.axios.get("/api/employees", { params: { page }}).then((res) => {
        this.to = res.data.to;
        this.from = res.data.from;
        this.current_page = res.data.current_page;
        this.total = res.data.total;
        this.employees = res.data.data;
      }).finally(() => {
        this.loading = false;
      })
    }
  }
}
</script>