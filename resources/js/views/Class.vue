<template>
  <Loading v-if="loading" />
  <div v-else class="mx-10">
    <div class="mb-8">
      <router-link to="/" class="inline-flex text-sm items-center gap-2 bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 -ml-1" fill="currentColor" viewBox="0 0 256 256">
          <path d="M224,128a8,8,0,0,1-8,8H59.31l58.35,58.34a8,8,0,0,1-11.32,11.32l-72-72a8,8,0,0,1,0-11.32l72-72a8,8,0,0,1,11.32,11.32L59.31,120H216A8,8,0,0,1,224,128Z"></path>
        </svg>
        Back to Employee Directory
      </router-link>
    </div>

    <div v-for="(schoolClass, classIndex) in classes" :key="classIndex"  class="bg-gray-100 p-8 mb-8 rounded-3xl">
      <div class="mb-8 flex justify-between items-center">
        <h3 class="font-black text-2xl">{{ formatDate(schoolClass.date) }}</h3>
        <span class="bg-green-100 text-green-600 font-bold text-sm tracking-wider px-4 py-2 rounded-lg border border-green-400">{{ schoolClass.classes.length }} Lessons</span>
      </div>

      <div v-for="(lesson, lessonIndex) in schoolClass.classes" :key="lessonIndex" class="bg-[#FFEAE3] mb-10 p-8 rounded-3xl">
        <div class="mb-4">
          <h4 class="font-black text-lg">{{ lesson.name }}</h4>
          {{ formatDuration(lesson.lesson.starts_at, lesson.lesson.ends_at) }}
        </div>

        <div class="grid grid-cols-4 gap-4">
          <div v-for="(student, studentIndex) in lesson.students" :key="studentIndex" class="p-4 rounded-3xl bg-white">
            <div class="flex gap-4 items-center">
              <div class="rounded-3xl bg-blue-500 h-16 w-16 flex items-center justify-center">
                <h2 class="text-lg text-white font-black tracking-widest">{{ `${student.student.forename[0]}${student.student.surname[0]}` }}</h2>
              </div>
              <h3 class="text-sm font-bold">{{ student.student.forename }} {{ student.student.surname }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Loading from '@/components/Loading.vue';

export default {
  components: {
    Loading
  },
  data() {
    return {
      loading: true,
      classes: []
    }
  },
  mounted() {
    this.axios.get(`/api/class/${this.$route.params.id}`).then((res) => {
      this.classes = res.data.employeeClasses;
    }).finally(() => {
      this.loading = false;
    });
  },
  methods: {
    formatDate(dateStr) {
      const date = new Date(dateStr);

      const options = {
        weekday: "long",
        day: "numeric",
        month: "long",
      };

      return date.toLocaleDateString("en-GB", options);
    },
    formatDuration(startDateStr, endDateStr) {
      const startDate = new Date(startDateStr);
      const endDate = new Date(endDateStr);

      const formattedStartDate = startDate.toLocaleTimeString('en-GB', {
        hour: 'numeric',
        minute: 'numeric',
        hour12: false
      });

      const formattedEndDate = endDate.toLocaleTimeString('en-GB', {
        hour: 'numeric',
        minute: 'numeric',
        hour12: false
      });

      return `${formattedStartDate} - ${formattedEndDate}`;
    }
  }
}
</script>