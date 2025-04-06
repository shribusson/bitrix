<template>
  <div class="vep-container__parent-block" :class="[classLevel, {'mlr': classLevel == 'lv5'}]">
    <div class="vep-container__node-dummy" :style="customDummyStyles"></div>
    <div v-if="hasChildren" class="vep-container__children-block" :class="[classLevel, showCkp ? 'mb' : '']">
      <VEPContainer v-for="(child, index) in node.children" :node="child" />
    </div>
    <VEPBlock v-if="showCkp" :node="node"></VEPBlock>
    <VEPBlock v-else-if="node.level == 4" :node="node"></VEPBlock>

    <!-- <VEPBlock v-else :node="node"></VEPBlock> -->
  </div>
</template>

<script>
import VEPBlock from "./VEPBlock.vue";
export default {
  name: 'VEPContainer',
  props: ['node'],
  components: {VEPBlock},
  computed: {
    hasChildren() {
      return this.node.hasOwnProperty('children') && this.node.children.length;
    },
    showCkp() {
      let show = false;
      if((this.node.level == 1 || this.node.level == 4) && this.node.hasOwnProperty('CKP')) {

        if(typeof this.node.CKP === 'string' && this.node.CKP.length) {
          show = true;
        }
      }
      return show;
    },
    classLevel() {
      return 'lv' + this.node.level;
    },
    customDummyStyles() {
      let style = {};
        if (this.node.hasOwnProperty('CUSTOM_WIDTH')) {
          if(parseInt(this.node.CUSTOM_WIDTH) > 300) {
            console.log(this.node.CUSTOM_WIDTH)
            style['width'] = (parseInt(this.node.CUSTOM_WIDTH) - 17) + 'px !important';
          }
        }
      return style;
    },
  }
}
</script>

<style scoped lang="scss">

* {
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}
/* .vep-container__parent-block.lv2 {
  margin-right: 60px;
} */
.vep-container__parent-block {
  position: relative;
  display: flex;
  flex-direction: column;
  &.lv1 {
    padding: 0 30px;
    & > .vep-block {
      text-align: center;
    }
  }
  &.lv2 {
    /* margin-right: 43px; */
  }
  &.lv2:last-child {
    margin-right: 0;
  }
  &.lv2:nth-child(2) .vep-container__parent-block.lv4 {
    /* -moz-margin-right: 60px;
    margin-right: 60px; */
  }
  &.lv2:not(:last-child) > .vep-block {
    margin-right: 60px;
  }
  
  /*&.lv2:nth-child(2) .vep-container__parent-block.lv3:last-child .vep-container__parent-block.lv4:last-child{
    margin-right: 0px;
  }*/
  &.lv2:last-child:not(:first-child) .vep-container__parent-block.lv3:last-child:not(:first-child) .vep-container__parent-block.lv4:last-child:not(:first-child){
    margin-right: 0;
  }

  &.lv4 {
    margin-right: 60px;

    .vep-block {
      min-height: 200px;
    }
  }
  &.lv2:last-child .vep-container__parent-block.lv3:last-child .vep-container__parent-block.lv4:last-child {
    margin-right: 0;
  }

  &.lv5:first-child {
    margin-left: 0 !important;
  }
  &.lv5:last-child {
    margin-right: 0 !important;
  }
}

.vep-container__parent-block.lv3 > .vep-block {
  margin-right: 60px;
}
.vep-container__parent-block.lv2:last-child:not(:first-child) .vep-container__parent-block.lv3:last-child > .vep-block {
  margin-right: 0;
}
/*.vep-container__parent-block.lv2:nth-child(2) .vep-container__parent-block.lv3:last-child > .vep-block {
  margin-right: 0;
}*/
/* .vep-container__parent-block.lv4:first-child {
  margin-right: 0px;
} */
.mlr {
  margin: 0 10px;
}
.vep-container__children-block:first-child {
  margin-left: 10px;
}
.vep-container__children-block {
  display: flex;
  position: relative;
  //background-color: #2fc6f6;
  &.lv1 {
    margin-bottom: 43px;
  }
  &.lv2 {
    height: 100%;
    /* margin-right: 60px; */
  }
  &.lv2.mb,
  &.lv3.mb {
    margin-bottom: 43px;
  }
  
}

.vep-container__node-dummy {
  width: 280px !important;
  /* height: 50px;
  background-color: red; */
  //padding: 0 10px;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}
</style>