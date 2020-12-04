<template>
<div class="content">
    <div class="container-fluid">
        <h1>All Comp Goes here... Counter: {{counter}} </h1>
        <compA> </compA>
        <compB> </compB>
        <compC> </compC>
        <Button type="info" @click="changeTheCounterNumber">Increase The Counter </Button>
    </div>
</div>
</template>

<script>

import compA from './compA'
import compB from './compB'
import compC from './compC'
import {mapGetters} from 'vuex'

export default {
    data(){
        return{

        }
    },
    computed:{
        ...mapGetters({
            'counter' : 'getTheConter'
        })
    },
    methods:{
        changeTheCounterNumber(){
            this.$store.dispatch('changeCounterAction',1)
            //this.$store.commit('changeTheNumber',1)
        }
    },
    components:{ compA,compB,compC},

}
</script>