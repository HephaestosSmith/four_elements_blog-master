import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import HeaderView from '../views/HeaderView.vue'
//import FooterView from '../views/FooterView.vue'
//import AboutView from '../views/AboutView.vue'
import RightListView from '../views/RightListView.vue'
import loginView from '../views/LoginView.vue'
import EditedView from '../views/EditedView.vue'
import ArticleView from '../views/ArticleView.vue'
import InstallView from '../views/InstallView.vue'
import CloseModalView from '../views/CloseModalView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    components: {
      default: HomeView,
      Header:HeaderView,
      //Footer:FooterView,
      RightList:RightListView,
      modal:CloseModalView
    },
    meta:{ installedAuth: true}
  },
  {
    path: '/install',
    name: 'install',
    components: {
      default: InstallView,
      Header:HeaderView
    },
    meta:{ installAuth: true}
  },
  {
    path: '/login',
    name: 'login',
    components: {
      default: HomeView,
      Header:HeaderView,
      //Footer:FooterView,
      RightList:RightListView,
      modal:loginView
    },
    meta:{installedAuth: true,loginAuth: true}
  },
  {
    path: '/edited/:UUID',
    name: 'edited',
    components: {
      default:HomeView,
      Header:HeaderView,
      //Footer:FooterView,
      RightList:RightListView,
      modal:EditedView
    },
    meta:{installedAuth: true,requiresAuth: true}
  },
  {
    path: '/article/:UUID',
    name: 'article',
    components: {
      default:HomeView,
      Header:HeaderView,
      //Footer:FooterView,
      RightList:RightListView,
      modal:ArticleView
    },
    meta:{installedAuth: true,requiresAuth: true}
  }
]

const router = createRouter({
  history: createWebHistory(),
  mode: 'history',
  routes
})

export default router
