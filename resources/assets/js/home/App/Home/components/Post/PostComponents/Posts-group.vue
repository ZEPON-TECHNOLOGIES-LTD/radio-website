<template>
    <div>
        <div class="row">
            <template v-for="post in posts">
                <post :key="post.id" :post-data="post"></post>
            </template>
        </div>
        <div class="row">
            <template v-if="pagination.next_page != null">
                <div class="col m4 offset-m4">
                    <p class="load-button center-align hoverable" @click="loadMorePosts()">Carregar mais</p>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import { http } from '../../../../../services'
import Post from './Post-item'

import { mapActions } from 'vuex'
import { isEmpty } from 'lodash'

export default {
    components: { Post },

    data(){
        return {
            posts: [],
            pagination: {}
        }
    },

    watch: {
        '$route': 'loadPosts'
    },

    methods: {
        ...mapActions([
            'hideLoader',
            'showLoader'
        ]),

        loadMorePosts() {
            this.showLoader()
            
            http.get(this.pagination.next_page)
            .then( ({ data }) => {
                this.posts.push(...data.data);
                this.pagination.next_page = data.next_page_url

                this.hideLoader()
            } ); 
        },

        loadPosts() {
            var url = "posts/getPosts"
            var categoryQuery = this.$route.query.categoria;

            if(!isEmpty(categoryQuery)){
                url = "posts/byCategory/" + categoryQuery
            }

            http.get(url)
            .then( ({ data }) => {
                this.posts = data.data
                this.pagination.next_page = data.next_page_url

                this.hideLoader()
            })
        }
    },

    created() {
        this.loadPosts()
    }
}
</script>

<style>

</style>
