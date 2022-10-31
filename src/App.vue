<template>
<div class="container-fluid" id="main"  :key="display">
  <div class="row bg-dark sticky-top"  id="Header">
    <div class="col-12">
     <router-view name="Header"/>
    </div>
  </div>
  <div class="row">
    <div class="d-none d-md-block col-md-2 bd-toc">
    </div>
    <div class="col-md-8 main">
       <router-view/>
    </div>
    <div class="d-none d-xl-block col-xl-2 bd-toc">
       <router-view name="RightList" />
    </div>
    
<!-- The Modal -->
<div class="modal fade" id="ModalView">
  <div class="modal-dialog modal-xl">
    <div class="modal-content bg-secondary">
      <!-- Modal Header -->
      <div class="col-sm-12" style="position: sticky;top: 10px;z-index: 1000;">
        <button type="button" class="close" data-dismiss="modal" style="color:red;">X</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body modal-body-scrollable">
       <router-view name="modal"/>
      </div>

      <!-- Modal footer
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" @click="close()" >Close</button>
      </div> -->

    </div>
  </div>
</div>
  </div>
  
</div>
</template>
<script>
import { useStore } from 'vuex'
import { Modal } from "bootstrap"

export default {
  provide(){    
    return {
      reload: this.reload,      
      conection: this.conection,
      modalshow:this.modalshow,
      PrismView:this.PrismView
    }
  },
  data() {
    return {
      display: 0
     }
  },
  created() {
     document.getElementsByTagName("body")[0].className="bg-secondary";
     this.useStore = useStore();
  },
  methods: {
    reload () {
      this.display++;
    },
    conection (data,response,command="Command") {
      let me = this;
      let useStore = me.useStore;
      let http = useStore.state.axios;
      let phpurl = useStore.getters.phpurl;

      http.post(phpurl(command),data)
      .then(response)
      .catch(function (error) {
       alert(error);
      });
    },
    modalshow(){
      let ModalView = new Modal(document.getElementById("ModalView"))
      if(document.getElementsByClassName("show").length == 0){
         ModalView.show();
      }
    }
  }
}
</script>
<style lang="scss">
#Footer{  
  background-color: rgb(45, 45, 45);
  color: rgb(112, 112, 112);
  text-align: left;
}
.article {
  background-color: rgba(245, 245, 245, 0.15);
  margin-top: 1rem;
  padding: 1rem 1rem 1rem 0.5rem;
}
.ck-editor__editable {
    min-height: 300px;
}
img{
  max-height: 100%;
  max-width: 100%;
}
@import './css/custom.css';
</style>
