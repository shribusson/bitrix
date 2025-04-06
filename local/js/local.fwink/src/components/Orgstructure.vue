<template>
  <div class="container" onselectstart="return false">
    <preloader :active="loading"></preloader>
    <div class="page-header">
      <div id="uiToolbarContainer" class="ui-toolbar">
        <div><h2 class="page-title">Структура компании</h2></div>
        <div class="ui-toolbar-right-buttons">
	        <action-list></action-list>
          <button class="ui-btn ui-btn-light scale-btn" @click="zoomIn">+</button>
          <div class="scale-val">{{currentScalePercent}}</div>
          <button class="ui-btn ui-btn-light scale-btn" @click="zoomOut">-</button>
          <span class="edit-title" v-if="canEdit">
            Режим
          </span>
          <Toggle
              v-if="canEdit"
              :value="editMode"
              checked-text="Редактирование"
              unchecked-text="Просмотр"
              checked-bg="#3bc8f5"
              unchecked-color="#000000"
              width="135"
              @click="editMode = !editMode" />
          <button v-if="editMode" class="ui-btn ui-btn-primary ui-btn-icon-add mg-l-15" @click="newDepartment">
            <span class="ui-btn-text">Добавить блок</span>
          </button>
        </div>
      </div>
    </div>
    <div class="chart">
      <helpBlock></helpBlock>
      <div class="chart-container" v-bind:style="{height: baseHeight * currentScale + (64 / currentScale) + 'px'}" ref="dragcontainer">
        <div class="chart-wrapper" ref="domcontainer" :style="initialTransformStyle">
          
          <chart-block v-if="isField" :node="departmentData" first-parent="true"/>
          <VEPContainer v-if="isField" :node="departmentData" />
        </div>
      </div>
    </div>
    
  </div>
</template>

<script>
import ChartBlock from "./ChartBlock.vue";
import helpBlock from "./helpBlock.vue";
import VEPContainer from "./VEPContainer.vue";
import { mapActions, mapGetters, mapState } from "vuex";
import Toggle from "./Toggle.vue";
import ActionList from "./ActionsList.vue";

const MATCH_TRANSLATE_REGEX = /translate\((-?\d+)px, ?(-?\d+)px\)/i
const MATCH_SCALE_REGEX = /scale\((\S*)\)/i

export default {
  name: "orgstructure",
  props: ['sign','signEdit','access'],
  components: {
	  ActionList,
    VEPContainer,
    Toggle,
    ChartBlock,
    helpBlock,
  },
  data() {
    return {
      treeConfig: { nodeWidth: 210, nodeHeight: 300, levelHeight: 320 },
      editMode: false,
      initTransformX: 0,
      initTransformY: 1,
      currentScale: 1,
      rights: {},
      popupActive: false,
      baseHeight: 5000
    };
  },
  computed: {
    ...mapGetters(["departmentData","loading","ajaxSign","ajaxSignEdit"]),
    ...mapState([['startX'], ['startY'], ['mode']]),
    isField() {
      return this.departmentData.hasOwnProperty("ID");
    },
    canEdit() {
      return this.rights.create || this.rights.update;
    },
    initialTransformStyle() {
      return {
        transform: `scale(1) translate(${this.initTransformX}px, ${this.initTransformY}px)`,
        transformOrigin: '2% 4%'
      }
    },
    currentScalePercent() {
      return Math.floor(this.currentScale * 100) + '%';
    }
  },
  created() {
    this.$store.commit("setSign", {
      sign: this.sign,
      signEdit: this.signEdit
    });
    if(this.access) {
      const access = JSON.parse(this.access);
      if(access) {
        this.rights = access;
      }
    }
	this.$root.$on('reload', () => {
		console.log('reload event');
		this.reload();
	});
  },
  mounted() {
    this.enableDrag();
    this.GET_STRUCTURE_API(this.sign);
    this.$refs.dragcontainer.addEventListener('mousewheel', (event) => {
      if (event.ctrlKey) {
        event.preventDefault()
        if (event.deltaY < 0) {
          this.zoomIn();
        } else {
          this.zoomOut();
        }
      }
    });
	  this.$nextTick(() => {
		  this.restoreScale();
	  });
  },
  watch: {
    departmentData() {
      //this.$root.$emit('reload');
      //this.restoreScale();
      this.$nextTick(() => {
          this.baseHeight = this.$refs.domcontainer.scrollHeight; /// this.currentScale;
            this.$nextTick(() => { window.BX24.fitWindow();});
      });
    },
    editMode(value) {
      this.$store.commit('setEditMode', value);
    }
  },
  methods: {
    ...mapActions(["GET_STRUCTURE_API"]),
    zoomIn() {
      const originTransformStr = this.$refs.domcontainer.style.transform
      let targetScale = 1 * 1.03
      const scaleMatchResult = originTransformStr.match(MATCH_SCALE_REGEX)
      if (scaleMatchResult && scaleMatchResult.length > 0) {
        const originScale = parseFloat(scaleMatchResult[1])
        targetScale *= originScale
      }
      this.setScale(targetScale)
    },
    zoomOut() {
      const originTransformStr = this.$refs.domcontainer.style.transform
      let targetScale = 1 / 1.03
      const scaleMatchResult = originTransformStr.match(MATCH_SCALE_REGEX)
      if (scaleMatchResult && scaleMatchResult.length > 0) {
        const originScale = parseFloat(scaleMatchResult[1])
        targetScale = originScale / 1.03
      }
      this.setScale(targetScale)
    },
    restoreScale() {
      this.setScale(0.75);
    },
    setScale(scaleNum) {
      if (typeof scaleNum !== 'number') return
      let pos = this.getTranslate()
      let translateString = `translate(${pos[0]}px, ${pos[1]}px)`
      this.$refs.domcontainer.style.transform =
          `scale(${scaleNum}) ` + translateString
      this.currentScale = scaleNum
      this.$store.commit('setCurrentScale', this.currentScale);
    },
    getTranslate() {
      let string = this.$refs.domcontainer.style.transform
      let match = string.match(MATCH_TRANSLATE_REGEX)
      if (match === null) {
        return [null, null]
      }
      let x = parseInt(match[1])
      let y = parseInt(match[2])
      return [x, y]
    },
    newDepartment() {
      window.BX24.openApplication({
        add: 'new',
        mode:'pages',
        page:'companyblock',
        bx24_width: 550
      }, BX.delegate(this.reload, this))
    },
    reload() {
      this.GET_STRUCTURE_API(this.sign);
    },
    enableDrag() {
      const container = this.$refs.dragcontainer;
      let startX = 0
      let startY = 0
      let isDrag = false
      let mouseDownTransform = ''
      container.onmousedown = (event) => {
        if(this.editMode) {
          if (event.target.closest('.user-block')) return
          if (event.target.closest('.control-block')) return
          if (event.target.closest('.title')) {
            let titleNode = event.target.closest('.title')

            if (titleNode.parentNode) {
              if (!titleNode.closest('.vep-block') && !titleNode.closest('.functions')) {
                return
              }
            } else {
              return
            }
          }
          if (event.target.closest('.open-link')) return
          if (event.target.closest('.photo_img')) return
        }
        mouseDownTransform = this.$refs.domcontainer.style.transform
        startX = event.clientX
        startY = event.clientY
        isDrag = true
        this.$refs.domcontainer.classList.add('noselectable');
      }
      container.onmousemove = (event) => {
        
        // if(this.userDrag) return
        if (!isDrag) return
        const originTransform = mouseDownTransform
        let originOffsetX = 0
        let originOffsetY = 0
        if (originTransform) {
          const result = originTransform.match(MATCH_TRANSLATE_REGEX)
          if (result !== null && result.length !== 0) {
            const [offsetX, offsetY] = result.slice(1)
            originOffsetX = parseInt(offsetX)
            originOffsetY = parseInt(offsetY)
          }
        }
        let newX =
            Math.floor((event.clientX - startX) / this.currentScale) +
            originOffsetX
        let newY =
            Math.floor((event.clientY - startY) / this.currentScale) +
            originOffsetY
        let transformStr = `translate(${newX}px, ${newY}px)`
        // console.log(originOffsetX, originOffsetY, transformStr)
        if (originTransform) {
          transformStr = originTransform.replace(
              MATCH_TRANSLATE_REGEX,
              transformStr
          )
        }
        this.$refs.domcontainer.style.transform = transformStr
      }

      document.onmouseup = (event) => {
        startX = 0
        startY = 0
        isDrag = false
        this.$refs.domcontainer.classList.remove('noselectable');
        /*let outerRect = this.$refs.dragcontainer.getBoundingClientRect()
        let innerRect = this.$refs.domcontainer.getBoundingClientRect()
        let outerCenterX = outerRect.left + outerRect.width/2;
        let outerCenterY = outerRect.top + outerRect.height/2;
        let innerPointX = -1*innerRect.left + outerCenterX;
        let innerPointY = -1*innerRect.top + outerCenterY;
        this.$refs.domcontainer.style.transformOrigin = innerPointX + 'px ' + innerPointY + 'px';*/
      }
    },
    initTransform() {
      if(this.initTransformX === 0 && this.initTransformY === 1) {
        this.$nextTick(function(){
          const containerWidth = this.$refs.domcontainer.getBoundingClientRect().width;
          const parentcontainerWidth = this.$refs.dragcontainer.getBoundingClientRect().width;
          this.initTransformX = -1*Math.floor(containerWidth / 2)*this.currentScale + Math.floor(parentcontainerWidth / 2)
          this.initTransformY = 100;
        });
      }
    },
    setHeightBottomBlock() {
      const container = this.$refs.dragcontainer;
      const block = document.querySelector('.bottom-block');
      // console.log(block);

      function heightPage(n) {
        return n.clientHeight;
      }
      function getBlock(arr) {
        let maxHeight = 0;
        arr.forEach(item => {
          if ( heightPage(item) > maxHeight) {
            maxHeight = heightPage(item);
          }
        })
        arr.forEach(item => item.style.height = `${maxHeight}px`);
      }
      return getBlock(block);
    },
  },
};
</script>
<style>
body {
  margin: 0px;
}
#app {
  /*position: absolute;*/
  width: 100%;
}
.popup-window {
  top: 300px !important;
  /*position: sticky !important;*/
}
.chart-wrapper {
  position: absolute !important;
}
.main-buttons {
  position: fixed;
  top: 0;
  z-index: 10;
  width: 100%;
  background: #ffffff;
}
</style>
<style scoped lang="scss">

.chart {
  position: relative;
  background: #f1f3f3;
}

.container {
  //height: 2000px;
  /*display: flex;
  flex-direction: column;
  align-items: center;*/
}
.page-header {
  width: 100%;
  background: #ffffff;
  /*overflow:hidden;*/
  position: fixed;
  z-index: 10;
  top: 68px;
}
.ui-toolbar {
  height: 48px;
  box-sizing: border-box;
  display: flex;
  align-items: center;
  justify-content: space-between;
  /*box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.1);*/
  padding-bottom: 3px;
}

.ui-toolbar-right-buttons {
  display: flex;
  align-items: center;
  margin-right: 15px;
}
.edit-title {
  margin: 0 15px;
  font-family: OpenSans-Semibold, "Helvetica Neue", Arial, Helvetica, sans-serif;
  color: #535c69;
  font-size: 12px;
}
h3 {
  margin-top: 32px;
  margin-bottom: 16px;
}
h2.page-title {
  margin-left: 25px;
  font-size: 18px;
  font-weight: 500;
  font-family: OpenSans-Semibold, "Helvetica Neue", Arial, Helvetica, sans-serif;
}
.scale-btn {
  font-family: OpenSans-Light, "Helvetica Neue", Arial, Helvetica, sans-serif;
  font-size: 30px;
  margin-right: 15px;
}
.mg-l-15 {
  margin-left: 15px;
}
.chart-container {
  width: auto;
  // height: 4800px;
  //overflow: hidden;
  overflow-y: hidden;
  overflow-x: scroll;
  position:relative;
  background: #f4f4f4;
}
.chart-wrapper {
  position: absolute;
}
.scale-val {
  margin-right: 15px;
  font-size: 20px;
  line-height: 30px;
  color: #535c69;
  font-weight: 500;
}
.noselectable {
  -webkit-touch-callout: none; /* iOS Safari */
  -webkit-user-select: none;   /* Chrome/Safari/Opera */
  -khtml-user-select: none;    /* Konqueror */
  -moz-user-select: none;      /* Firefox */
  -ms-user-select: none;       /* Internet Explorer/Edge */
  user-select: none;
}
</style>