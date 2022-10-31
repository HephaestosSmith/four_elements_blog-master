import { createApp } from 'vue'
import App from './App.vue'
import store from './store'
import router from './router'
import "bootstrap" 
import "bootstrap/scss/bootstrap.scss"
import axios from 'axios'
import Cookies from 'vue-cookie'
import CKEditor from '@ckeditor/ckeditor5-vue'

axios.defaults.baseURL='/api'

const app = createApp(App)

router.install

router.beforeEach((to, from, next) => {
      if (to.meta.installAuth) {  // 若要前往的頁面具有installedAuth的話，就不會放行
        let data = new URLSearchParams();
        data.append('name',to.name);
        data.append('param',JSON.stringify(to.params));
        axios.post('/controllers/install.php',data).then((response) => {
          if (response.data.success == "1") {  // 若成功安裝→放行
            next({path: '/'});
          }
          else{
            next();                            // 若前往安裝頁面→放行
          }
        });
      }else if (to.meta.loginAuth) {  // 若要前往的頁面具有requiresAuth的話，就不會放行
        let data = new URLSearchParams();
        data.append('commandType', "check");
        data.append('username', Cookies.get('username'));
        data.append('TOKEN', Cookies.get('TOKEN'));
        axios.post('/controllers/Command.php',data).then((response) => {  // 因不是在vue下執行此元件，所以此處的this.$http使用axios替代
          if (response.data.success == "0") {  // 若成功登入→放行；若非登入狀態，則會跳回登入頁面
            Cookies.delete('username');
            Cookies.delete('TOKEN');
            next();
          }
          else{
            next({path: '/'});
          }
        });
      }else if(to.meta.requiresAuth){
        let data = new URLSearchParams();
        data.append('commandType', "checkPOWER");
        data.append('username', Cookies.get('username'));
        data.append('TOKEN', Cookies.get('TOKEN'));
        data.append('UUID',to.params.UUID)
        axios.post('/controllers/Command.php',data).then((response) => {  // 因不是在vue下執行此元件，所以此處的this.$http使用axios替代
          if (response.data.success == "1") {  // 若成功登入→放行；若非登入狀態，則會跳回登入頁面
            next();
          }else{
            next({path: '/'});
          }
        });
      }else{
        next();
      }
      if (to.meta.installedAuth) {  // 若要前往的頁面具有installedAuth的話，就不會放行
          let data = new URLSearchParams();
          data = new URLSearchParams();
          data.append('commandType',"getTitle");
          data.append('name',to.name);
          data.append('param',JSON.stringify(to.params));
          axios.post('/controllers/Command.php',data).then((response) => {
            if (response.data.success == "1") {  // 若成功安裝→放行
              let title = response.data.result.title;
              document.title = title;
            }else{
              next({path: '/install'});
            }
          }).catch(function (error){
            alert(error);
          });
        }

    });

app.use(router).use(store).use(CKEditor).mount('#app');

