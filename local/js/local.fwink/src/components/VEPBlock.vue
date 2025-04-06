<template>
  <div class="vep-block" :style="customStyle">
    <div class="border">
      <div class="title">
        {{blockTitle}}
      </div>
      <div class="content" v-html="content"></div>
    </div>
  </div>
</template>

<script>

export default {
  name: 'VEPBlock',
  props: {
    title: {
      type: String,
      default: ''
    },
    node: Object
  },
  data() {
    return {

    }
  },
  computed: {
    customStyle() {
      let styles = {};
      if (this.node.COLOR_BLOCK.length) {
        styles['background-color'] = this.node.COLOR_BLOCK;
      }
      return styles;
    },
    blockTitle() {
      return this.title.length ? this.title : 'ЦКП';
    },
    content() {
      return this.node.CKP;
    }
  }
}
</script>

<style scoped lang="scss">
.vep-block {
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
  background: #ffffff;
  border-radius: 4px;
  padding: 10px;
  //margin: 10px;
  display: flex;
  height: 100%;
  /*max-height: 200px;*/
  overflow-y: auto;
  text-transform: uppercase;
  font-weight: bold;
  font-family: OpenSans-Semibold, "Helvetica Neue", Arial, Helvetica, sans-serif;

  .border {
    border: 1px solid #dddddd;
    border-radius: 8px;
    width: 100%;
    padding:15px;

    .content {
      overflow: hidden;
      position: relative;
        word-break: break-word;
	    white-space: break-spaces;
    }
  }
}
</style>