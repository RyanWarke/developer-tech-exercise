import { createApp } from 'vue';
import axios from 'axios';
import VueAxios from 'vue-axios';
import App from './App.vue';
import router from './router';
import pluralize from './filters/pluralize';
import ToastPlugin from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-bootstrap.css';

const app = createApp(App);
app.use(router);
app.use(VueAxios, axios);
app.use(ToastPlugin, { position: 'top-right' });

app.config.globalProperties.$filters = {
  pluralize
};

app.mount('#app');
