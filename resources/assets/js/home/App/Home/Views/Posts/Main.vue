<template>
<div class="row">
    <div class="col m12">
        <posts></posts>
    </div>

    <!-- MOSTRA COLUNA DA ESQUERDA NA PARTE DE BAIXO QUANDO É MOBILE -->
    <div class="col m4 hide-on-large-only show-on-small">
        <today-programming></today-programming>
        <supporters side="left"></supporters>        
    </div>
</div>
</template>

<script>
import Posts from '../../components/Post/PostComponents/Posts-group.vue'
import Supporters from '../../components/Supporter/Supporters'
import TodayProgramming from '../../components/Programming/TodayProgramming'

export default {
    components: { Posts, Supporters, TodayProgramming }
}
</script>