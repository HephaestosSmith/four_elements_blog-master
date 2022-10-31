<template>
    <div class="row">
      <div class="col d-flex justify-content-center">
        <div class="spinner-grow text-primary m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-success m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-danger m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-warning m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-info m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-light m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-dark m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <!--
        <div class="spinner-border text-primary m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-primary m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-secondary m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-success m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-danger m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-warning m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-info m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-light m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-dark m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>-->
      </div>
    </div>
</template>

<script>
import { useStore } from 'vuex'
export default {
  created() {
     useStore().commit('ModalViewColse');
  }
}
</script>
