<template>
      <div class=" rounded article" style="overflow: hidden;">
        <h5>分類</h5>
        <div class="menulist" v-for="(item,index) in MAINCATEGORYS" :key="index">
        <div class="menutitle" @click="item.ISSHOW = !item.ISSHOW">
          {{item.CATEGORYNAME}}
        </div>
        <div class="submenulist" :class="{ menushow: item.ISSHOW }" >
          <div class="submenutitle" v-for="(subitem,subindex) in item.SUBCATEGORYS" :key="subindex" @click="SearchCATEGORY(subitem.CATEGORYNAME)">{{subitem.CATEGORYNAME}}</div>
        </div>
        </div>
      </div>
</template>
<script>
import { useStore } from 'vuex'

export default {
  inject: [
     'conection',
     'modalshow'
     ],
  data() {
      return {
          MAINCATEGORYS:[],
          useStore:[]
      };
  },
  watch:{
       "useStore.state.MAINCATEGORYS": function (){
        this.MAINCATEGORYS = this.useStore.state.MAINCATEGORYS;
       }
  },
  created(){
     let me = this;
     me.useStore = useStore();
  },
  methods:{
  SearchCATEGORY(CATEGORY){
    let me = this;
    let state = me.useStore.state;
    state.homeloadflag = true;
    state.list = [];
 
    state.SEARCHTYPE = "CATEGORY";
    state.KEYWORD = CATEGORY;
    let data = new URLSearchParams();
    data.append('commandType', "getAticle");
    data.append('SEARCHTYPE', state.SEARCHTYPE);
    data.append('KEYWORD', state.KEYWORD);
    
    me.conection(data,function(response){
     state.homeloadflag = false;
     let success = response.data.success;
     if (success == "1"){
         let result = response.data.result;
            result.forEach(element => {
                state.list.push(element);
            });
            state.noDataFlag = false;
         }
     })
   }
  }
}
</script>