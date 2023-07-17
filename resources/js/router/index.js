import { createRouter, createWebHistory } from 'vue-router';

const routes = [
  {
    path: '/',
    name: 'employee-directory',
    component: () => import('../views/EmployeeDirectory.vue'),
  },
  {
    path: '/class/:id',
    name: 'class',
    component: () => import('../views/Class.vue'),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
