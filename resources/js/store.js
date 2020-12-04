import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex)
export default new Vuex.Store({
    state:{
        counter:1000,
        deleteModalObj:{
            showDeleteModal: false,
            deleteUrl: '',
            data: null,
            deleteIndex:-1,
            isDeleted:false,
        },
        user: false,
        userPermission:null,
    },
    getters:{
        getTheConter(state){
            return state.counter
        },
        getDeleteModalObj(state){
            return state.deleteModalObj
        },
        getUserPermission(state){
            return state.userPermission
        }
    },
    mutations:{
        changeTheNumber(state,data){
            state.counter += data
        },
        setDeleteModal(state,data){
            const deleteModalObj = {
                showDeleteModal: false,
                deleteUrl: '',
                data: null,
                deleteIndex:-1,
                isDeleted:data,
            }
            state.deleteModalObj = deleteModalObj
            //state.deleteModalObj.showDeleteModal = false
            //state.deleteModalObj.isDeleted = data
        },
        setDeletingModalObj(state,data){
            state.deleteModalObj = data
        },
        setUpdateUser(state,data){
            state.user = data
        },
        setUserPermission(state, data){
            state.userPermission = data
        }
    },
    actions: {
        changeCounterAction({commit},data){
            commit('changeTheNumber',data);
        }

    }
});