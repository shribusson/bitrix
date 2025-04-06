<template>
<!-- @drop='onDrop($event, "", node)'
    @dragenter="onDragEnter($event, node, '')"
    @dragleave="onDragLeave($event, '')"
    @dragover.prevent -->
  <div class="parent-block" :class="classLevel"
    
  >
    <!-- <div class="droppable-overlay" v-if="isDropBlockActive === node.ID">
      <div class="droppable-overlay-title">Переместить блок</div>
    </div> -->
    <card-departament
        :node-emp="node"
        hide-ckp="true"
        :has-children="hasChildren"
        @reload="reload"
    ></card-departament>
    <div v-if="hasChildren" class="children-block" :class="classLevel">
      <chart-block v-for="(child, index) in node.children"
                   :node="child"
                   :siblings-count="node.children.length"
                   :index="index">
      </chart-block>
    </div>
    <div class="top-line-vertical" v-if="!firstParent"></div>
    <div v-if="siblingsCount > 1" class="top-line-horizontal" :class="{rightline: isFirstSibling, leftline: isLastSibling, fullline: isCenterSibling}"></div>
  </div>
</template>

<script>
import CardDepartament from "./CardDepartament.vue";
import { mapState, mapGetters } from "vuex";
export default {
  name: 'ChartBlock',
  props: ['node','siblingsCount','index','firstParent'],
  components: {CardDepartament},
  data() {
    return {
      isDropBlockActive: 0,
      parentBlock: '',
    }
  },
  computed: {
    ...mapState([['departmentData']]),
    ...mapGetters(['editMode']),
    hasChildren() {
      return this.node.hasOwnProperty('children') && this.node.children.length;
    },
    isFirstSibling() {
      return this.index === 0;
    },
    isLastSibling() {
      return this.index === this.siblingsCount - 1;
    },
    isCenterSibling() {
      return this.index > 0 && this.index < this.siblingsCount - 1;
    },
    classLevel() {
      return 'lv' + this.node.level;
    }
  },
  methods: {
    /*onDrop($event, x, node) {
      console.log($event, x, node)
    },
    onDragEnter($event, node, y) {
      this.isDropBlockActive = node.ID
      console.log(this.departmentData.ID, node.ID)
      let obj = this.departmentData
      let lengthArr = 0
      let countFor = 0
      
      console.log(this.findNode(node.ID, this.departmentData))
    },
    onDragLeave($event, x) {
      console.log($event, x)
    },*/
    reload() {
      this.$root.$emit('reload');
    },
  }
}
</script>

<style scoped lang="scss">
.parent-block {
  position: relative;
  display: flex;
  width: 100%;
  flex-direction: column;
  // .droppable-overlay {
  //   border-radius: 8px;
  //   border: 2px dashed #f5f5f5;
  //   background: rgba(0, 0, 0, 0.2);
  //   width: 100%;
  //   min-height: 200px;
  //   position:absolute;
  //   top: 0;
  //   left: 0;
  //   .droppable-overlay-title {
  //     position: absolute;
  //     text-align: center;
  //     color: #f5f5f5;
  //     font-size: 16px;
  //     font-weight: 400;
  //     top: calc(50% - 10px);
  //     width: 100%;
  //   }
  // }
  .top-line-vertical {
    position: absolute;
    width: 2px;
    height: 25px;
    top: -25px;
    background: #cecece;
    left: calc(50% - 1px);
  }
  &.lv1 > .mainDep > .container{
    text-align: center;
    width: 580px;
  }
  &.lv2 > .mainDep > .container{
    text-align: center;
    width: 580px;
  }
  &.lv3 > .mainDep > .container{
    text-align: center;
    width: 580px;
  }
  &.lv4 > .mainDep > .container{
    text-align: center;
    width: 580px;
  }
  // &.lv5 {
    
  //   // margin: 0 -8px;
  // }
}
.children-block {
  display: flex;
  position: relative;
  flex-grow: 1;
  /*padding: 0 15px;*/

  &.lv4 {
    padding: 0 20px;
  }

  .top-line-horizontal {
    position: absolute;
    height: 2px;
    top: -26px;
    background: #cecece;

    &.rightline {
      width: 50%;
      right: 0;
    }
    &.leftline {
      width: 50%;
      left: 0;
    }
    &.fullline {
      width: 100%;
      left: 0;
    }
  }
}
</style>