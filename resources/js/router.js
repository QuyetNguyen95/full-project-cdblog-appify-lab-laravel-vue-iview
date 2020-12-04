import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)
import UseComp from './vuex/useComp'



//Admin Pages

import HomePage from './components/pages/HomePage'
import LoginPage from './admin/pages/LoginPage'
import TagsPage from './admin/pages/TagsPage'
import CategoriesPage from './admin/pages/CategoriesPage'
import CreateBlogPage from './admin/pages/CreateBlogPage'
import AdminUsersPage from './admin/pages/AdminUsersPage'
import RolePage from './admin/pages/RolePage'
import AssignRole from './admin/pages/AssignRole'

const routes = [

    //Project Routes....
    
    {
        path: '/',
        component: HomePage,
        name: 'home'
    },
    {
        path: '/login',
        component: LoginPage,
        name: 'login'
    },
    {
        path: '/tags',
        component: TagsPage,
        name: 'tags'
    },
    {
        path: '/categories',
        component: CategoriesPage,
        name: 'categories'
    },
    {
        path: '/createBlog',
        component: CreateBlogPage,
        name: 'createBlog'
    },
    {
        path: '/adminusers',
        component: AdminUsersPage,
        name: 'adminusers'
    },
    {
        path: '/roles',
        component: RolePage,
        name: 'roles'
    },
    {
        path: '/assignrole',
        component: AssignRole,
        name: 'assignrole'
    },





    {
        path: '/testvuex',
        component: UseComp
    },
];

export default new VueRouter({
    mode : 'history',
    routes
})