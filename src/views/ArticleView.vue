<template>
  <AticleItem :uuid="UUID" :key="UUID"/>
</template>

<script>
import AticleItem from '../components/AticleItem.vue'

export default {
  data(){
    return {
      UUID: this.$route.params.UUID
    }
  }
  ,
  components: {
    AticleItem
  },
  updated(){
      this.UUID = this.$route.params.UUID
  }
}
</script>