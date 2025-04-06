<template>
  <div :id="'user' + user.ID" v-if="user" class="user-block" :draggable="this.editMode" @dragstart="startDrag($event, user, postId)" @dragend="dragEnd($event, user)">
    <a draggable="false" class="photo" href="javascript:void(0)" :title="fullName" @mousedown="openUserMouseDown($event)" @click="openUser($event)">
      <img draggable="false" class="photo_img" v-if="hasImage" :src="user.PERSONAL_PHOTO" />
    </a>
    <a draggable="false" class="name" href="javascript:void(0)" v-if="isLarge" @mousedown="openUserMouseDown($event)" @click="openUser($event)">{{fullName}}</a>
  </div>
</template>

<script>
import { mapState, mapGetters } from "vuex";
export default {
  name: "CardDepartament",
  props: ['user', 'isLarge', 'postId'],
  data() {
    return {
      pointerPositionX: 0,
      pointerPositionY: 0
    }
  },
  computed: {
    ...mapGetters(["ajaxSign",'editMode']),
    ...mapState([['mode'], ['currentScale']]),
    userName() {
      return this.user?.NAME ?? '';
    },
    userLastName() {
      return this.user?.LAST_NAME ?? '';
    },
    userSecondName() {
      return this.user?.SECOND_NAME ?? '';
    },
    fullName() {
      return [this.userLastName,this.userName,this.userSecondName].join(' ').trim();
    },
    hasImage() {
      return this.user.hasOwnProperty('PERSONAL_PHOTO');
    }
  },
  methods: {
    openUserMouseDown(event) {
      this.pointerPositionX = event.screenX;
      this.pointerPositionY = event.screenY;
    },
    openUser() {
      if(event.screenX !== this.pointerPositionX || event.screenY !== this.pointerPositionY) {
        return;
      }
      /*window.BX24.openApplication({
        mode: 'pages',
        page: 'staff',
        element_id: this.user.ID,
        "link_utm": this.ajaxSign,
        bx24_width: 870
      });*/
      window.BX24.openPath('/company/personal/user/' + this.user.ID + '/');
    },
    /*Drag and Drop*/

    startDrag(event, item, id) {
      console.log('dragStart', this.mode)
      if(this.editMode) {
        // this.$store.commit('setUserDrag', true);
        this.$store.commit('setDropMode', 'user');
        if (event.getModifierState("Shift")) {
          this.dragMove(event, item, id)
        } else if(event.getModifierState("Control")) {
          this.dragCopy(event, item, id)
        } else {
          this.dragMoveClear(event)
          event.dataTransfer.dropEffect = 'none'
          event.dataTransfer.effectAllowed = 'none'
          return false
        }
      } else {
        event.dataTransfer.dropEffect = 'none'
        event.dataTransfer.effectAllowed = 'none'
      }
    },
    dragCopy(event, item, id) {
      let j = JSON.stringify(item)
      event.dataTransfer.dropEffect = 'copy'
      event.dataTransfer.effectAllowed = 'copy'
      event.dataTransfer.setData('type', 'copy')
      event.dataTransfer.setData('oldPostID', id)
      event.dataTransfer.setData('userID', item.ID)
      event.dataTransfer.setData('user', j)
    },
    dragMoveClear(event) {
      console.log('dragMoveClear')
      let clearElem = document.querySelector('.clearBlock')
      event.dataTransfer.setDragImage(clearElem, 0, 0);
    },
    dragMove(event, item, id) {
      // Создаем клон перетаскиваемого юзера
      let elem = document.getElementById('user' + this.user.ID)
      let prev = elem.querySelector('.photo')
      let clone = elem.cloneNode(true);
      // Масштабируем клона и назначаем стили
      clone.classList.add('targetBlock')
      clone.style.zoom = this.currentScale
      clone.style.width = 100 * this.currentScale + '%'
      clone.style.height = 100 * this.currentScale + '%'
      // Даем клону id и засовываем в оригинал
      clone.id = 'clone'
      prev.prepend(clone);
      // Добавляем данные в event
      event.dataTransfer.setDragImage(clone, event.offsetX * this.currentScale , event.offsetY);
      let j = JSON.stringify(item)
      event.dataTransfer.dropEffect = 'move'
      event.dataTransfer.effectAllowed = 'move'
      event.dataTransfer.setData('type', 'move')
      event.dataTransfer.setData('oldPostID', id)
      event.dataTransfer.setData('userID', item.ID)
      event.dataTransfer.setData('user', j)
    },
    dragEnd(event, user) {
      // Удаляем клон
      let element = document.getElementById('clone')
      if(element) {
        element.parentNode.removeChild(element);
      }
      // this.$store.commit('setUserDrag', false);
      this.$store.commit('setDropMode', '');
      // Удаляем элемент
      if (event.dataTransfer.dropEffect == 'move') {
        this.$emit('removeUser', user.ID);
      }
      
      // console.log('dragEnd', this.mode)
    }
  }
}
</script>

<style scoped lang="scss">
.targetBlock {
  position: absolute !important;
  width: 100%;
  top: 0;
  left: 0px;
  // border: 1px solid black;
  // background: blue;
  z-index: -1;
}
.user-block {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  margin-bottom: 5px;

  .photo {
    display: block;
    width: 22px;
    min-width: 22px;
    height: 22px;
    border-radius: 50%;
    background-color: #7b8691;
    background-size: contain;
    overflow: hidden;
    margin-right: 4px;

    img {
      width: 100%;
      height: 100%;
    }
  }

  .name {
    color: #131313;
    font-family: OpenSans-Regular, "Helvetica Neue", Arial, Helvetica, sans-serif;
    text-decoration: none;
    //font-size: 10px;
    margin-left: 10px;
    font-weight: bold;
    text-align: left;
  }
}
</style>