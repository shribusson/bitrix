<template >
  <div :id="'parent' + nodeEmp.ID" class="mainDep">
    <div class="container" :class="{long: longBlock}" :style="customContainerStyles"
    >
      <CardBlock 
        v-if="!hide"
        :nodeEmp="nodeEmp"
        hideCkp="true"
        :hasChildren="hasChildren"
        @reload="reload"
        >

      </CardBlock>
      <div class="section hide" v-else>
        <div class="line"></div>
      </div>
      <div class="bottom-line" v-if="hasChildren"></div>
    </div>
    <!-- Окошко для перетаскивания блока -->
    <div class="mainCardDep" v-if="mode == 'block' && !longBlock"
      @drop='onDrop($event, "", nodeEmp)'
      @dragenter="onDragEnter($event, '', '', mode)"
      @dragleave="onDragLeave($event, '')"
      @dragover.prevent>
      <div class="droppable-overlay" v-if="isDropActiveId == nodeEmp.ID">
        <div class="droppable-overlay-title">{{dropAreaText}}</div>
      </div>
    </div>
    <!-- <div class="targetBlock">
      <p>sdfsdf</p>
    </div> -->
    <div class="clearBlock">
    </div>
  </div>
  
</template>

<script>
import Icon from "./Icon.vue";
import UserList from "./UserList.vue";
//import CardSection from "./CardSection.vue";
import CardBlock from "./CardBlock.vue";
import { mapState, mapActions, mapGetters } from "vuex";
export default {
  name: "CardDepartament",
  data() {
    return {
      idPost: '',
      ckpParams: {
        top: 0,
        left: 0,
        width: 0
      },
      isDropActive: 0,
      isDropPostActive: 0,
      // isDropBlockActive: 0,
      // dropAreaText: '',
      result_found: false,
      searchBlock: '',
      lvl: 0,
    };
  },
  computed: {
    ...mapGetters(["ajaxSign","ajaxSignEdit","editMode"]),
    ...mapState([['mode'], ['departmentData'], ['currentScale'], ['isDropActiveId'], ['dropAreaText']]),
    canEdit() {
        // todo: rights
      return !this.longBlock;
    },
    customBlockStyles() {
      let style = {};
      if(this.longBlock) {
        if (this.nodeEmp.parentItem.COLOR_BLOCK.length) {
          style['background-color'] = this.nodeEmp.parentItem.COLOR_BLOCK;
        }
      } else {
        if (this.nodeEmp.COLOR_BLOCK.length) {
          style['background-color'] = this.nodeEmp.COLOR_BLOCK;
        }
      }
      return style;
    },
    customContainerStyles() {
      let style = {};
      if(this.longBlock) {
        if (this.nodeEmp.parentItem.hasOwnProperty('CUSTOM_WIDTH')) {
          if(parseInt(this.nodeEmp.parentItem.CUSTOM_WIDTH) > 0)
            style['width'] = this.nodeEmp.parentItem.CUSTOM_WIDTH + 'px';
        }
      } else {
        if (this.nodeEmp.hasOwnProperty('CUSTOM_WIDTH')) {
          if(parseInt(this.nodeEmp.CUSTOM_WIDTH) > 0) {
            style['width'] = this.nodeEmp.CUSTOM_WIDTH + 'px';
          }
        }
      }
      return style;
    },
    ckpBackground() {
      let color = '';
      if (this.nodeEmp.COLOR_BLOCK.length) {
        color = this.nodeEmp.COLOR_BLOCK;
      }
      return color;
    },
    customHeaderStyles() {
      let style = {};
      if(this.longBlock) {
        if (this.nodeEmp.parentItem.COLOR_HEADER.length) {
          style['background-color'] = this.nodeEmp.parentItem.COLOR_HEADER;
        }
      } else {
        if (this.nodeEmp.COLOR_HEADER.length) {
          style['background-color'] = this.nodeEmp.COLOR_HEADER;
        }
      }
      return style;
    },
    hide() {
      return this.nodeEmp.hasOwnProperty('IS_HIDE') && this.nodeEmp.IS_HIDE === true && !this.editMode;
    },
    showCkp() {
      return false;
    },
    wideBlock() {
      return false; //parseInt(this.nodeEmp.level) <= 4;
    },
    longBlock() {
      return parseInt(this.nodeEmp.level) === 6;
    }
  },
  mounted() {
    
  },
  methods: {
    ...mapActions(["SET_DROP_ELEMENT"]),
    getDepartmentCkpParams() {
      let xmin = null;
      let xmax = null;
      let y = null;
      let dy  = null;
      let width = 0;
      let bottomKey = '';

      function checkChildren(nodeList) {
        nodeList.forEach(function (node) {
          if (xmin === null || node.x < xmin) {
            xmin = node.x;
          }
          if (xmax === null || node.x > xmax) {
            xmax = node.x
          }
          if (y === null || node.y >= y) {
            y = node.y;
            bottomKey = node.data._key;
            let nodeHeight = document.querySelector('[data-key="' + bottomKey + '"]').scrollHeight;
            if(dy === null || nodeHeight >= dy) {
              dy = nodeHeight;
            }
          }


          if(node.hasOwnProperty('children') && node.children.length) {
            checkChildren(node.children);
          }
        });
      }
      checkChildren(this.treeNode.children)

      let left = xmin - this.treeNode.x;
      if(left > 0) {
        left = -left;
      }
      left += 25; // +margin
      if(this.treeNode.children.length >= 2) {
        left += 157.5; // +wide block offset
      }

      width = xmax - xmin;
      if(width < 0) {
        width= -width;
      }
      width += 160; // +block section width

      y += 25;

      if(this.nodeEmp.level === 1) {
        y += 100;
        y += dy;
      }

      if(this.nodeEmp.level === 4) {
        y += 420; // Min block height + 100
      }

      this.ckpParams =  {
        left: left + 'px',
        top: y + 'px',
        width: width + 'px'
      }
    },
    openPostClick(event) {
      this.openPost();
    },
    addChildrenClick(event) {
      this.addChildren();
    },
    editBlockClick(event) {
      this.editBlock();
    },
    removeBlockClick(event) {
      this.removeBlock();
    },
    openPost(id) {
      id = id || this.postData.id;
      window.BX24.openApplication({
        mode: 'pages',
        page: 'post',
        element_id: id,
        sign: this.ajaxSign,
        bx24_width: 550
      }, BX.delegate(this.reload, this))
    },
    addChildren(parentId) {
      parentId = parentId || this.nodeEmp.ID;
      window.BX24.openApplication({
        add: 'new',
        mode: 'pages',
        page: 'companyblock',
        parent_id: parentId,
        sign: this.ajaxSign,
        bx24_width: 550
      }, BX.delegate(this.reload, this));
    },
    editBlock(id) {
      id = id || this.nodeEmp.ID;
      window.BX24.openApplication({
        mode: 'pages',
        page: 'companyblock',
        element_id: id,
        sign: this.ajaxSign,
        "signedParams": this.ajaxSignEdit,
        bx24_width: 550
      }, BX.delegate(this.reload, this));
    },
    removeBlock(id, name) {
      id = id || this.nodeEmp.ID;
      name = name || this.nodeEmp.NAME;
      let popup = new BX.UI.Dialogs.MessageBox(
          {
            title: 'Удаление',
            message: 'Вы действительно хотите удалить блок ' + name + '? Дочерние блоки поднимутся на уровень текущего.',
            modal: true,
            buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
            onOk: BX.delegate(function (messageBox) {
              messageBox.close();
              this.$root.$store.commit('enableLoading');
              BX.ajax({
                method: 'POST',
                dataType: 'json',
                url: '/local/components/local/fwink.companyblock.edit/ajax.php',
                data: {
                  "AJAX_CALL": "Y",
                  "action": "delete",
                  "ID": id,
                  "sign": this.ajaxSign,
                  "signedParams": this.ajaxSignEdit
                },
                onsuccess: BX.delegate(function (result) {
                  this.$root.$store.commit('disableLoading');
                  if(result.hasOwnProperty('status')) {
                    if(result.status == 'success') {
                      this.reload();
                    } else if(result.status == 'error') {
                      this.processError(result.message, 'Удаление блока')
                    }
                  } else {
                    this.processError('Внутренняя ошибка');
                  }
                }, this)
              });
            }, this),
            onCancel: function (messageBox) {
              messageBox.close();
            }
          });
      popup.show();
	  window.BX24.scrollParentWindow(0);
    },
    processError: function(message, title) {
      message = message || '';
      title = title || 'Изменение блока'
      let popup = new BX.UI.Dialogs.MessageBox(
          {
            title: title,
            message: 'Ошибка: ' + message,
            modal: true,
            buttons: BX.UI.Dialogs.MessageBoxButtons.OK,
            onOk: BX.delegate(function (messageBox) {
              messageBox.close();
            }, this)
          });
      popup.show();
	  window.BX24.scrollParentWindow(0);
    },
    reload() {
		if(this.editMode) {
			this.$root.$emit('reload');
		}
    },
    find(blockId, toBlockId, currentNode, action, color) {
      console.log('find', blockId, toBlockId, currentNode, action, color)
        // ищем перетаскиваемый блок, удаляем уго у родителя и записываем его в searchBlock
        this.findNode(blockId, currentNode, action, color)
        this.result_found = false
        // ищем блок в который перетаскиваем и записываем  в него searchBlock
        this.findNode(toBlockId, currentNode, 'add', color)
        this.searchBlock = ''
        this.result_found = false
    },
    takeChild(currentNode, color) {
      let currentChild;
      currentNode.level = this.lvl
      if(currentNode.COLOR_BLOCK) {
        currentNode.COLOR_BLOCK = color
      }
      if(currentNode.children) {
        if(currentNode.level == 5) {
          // проходимся по children и засовываем их в children[0].child 
          let arr = []
          for(let i = 0; i < currentNode.children.length; i++) {
            arr.push(currentNode.children[i])
          }
          currentNode.children = []
          currentNode.children[0] = {}
          currentNode.children[0].NAME = ''
          currentNode.children[0].NUMBER = ''
          currentNode.children[0].childs = arr
          currentNode.children[0].parentItem = {}
          currentNode.children[0].level = 6
          if(!currentNode.children[0].CUSTOM_WIDTH) {
            currentNode.CUSTOM_WIDTH = ''
          }
          currentNode.children[0].parentItem = {}
          currentNode.children[0].parentItem.COLOR_BLOCK = currentNode.COLOR_BLOCK
          this.lvl++
          for(let i = 0; i < currentNode.children[0].childs.length; i++) {
            currentChild = currentNode.children[0].childs[i];
            this.takeChild(currentChild, color)
          }
          this.lvl--
        } else if(currentNode.children[0].childs) {
          // проходимся по child и засовываем их в children и удаляем children[0]
          for(let i = 0; i < currentNode.children[0].childs.length; i++) {
            currentNode.children[0].childs[i].COLOR_BLOCK = color
            currentNode.children.push(currentNode.children[0].childs[i])
          }
          currentNode.children.splice(0, 1)
          this.lvl++
          for(let i = 0; i < currentNode.children.length; i++) {
            currentNode.children[i].COLOR_BLOCK = color
            currentChild = currentNode.children[i];
            this.takeChild(currentChild, color)
          }
          this.lvl--
        } else if(currentNode.children[0].parentItem) {
          delete currentNode.children
        } else {
          this.lvl++
          for(let i = 0; i < currentNode.children.length; i++) {
            currentNode.children[i].COLOR_BLOCK = color
            currentChild = currentNode.children[i];
            this.takeChild(currentChild, color)
          }
          this.lvl--
        }
      } else if(!currentNode.children && currentNode.level == 5) {
        currentNode.children = []
        currentNode.children[0] = {}
        currentNode.children[0].NAME = ''
        currentNode.children[0].NUMBER = ''
        currentNode.children[0].parentItem = {}
        currentNode.children[0].level = 6
        if(!currentNode.children[0].CUSTOM_WIDTH) {
          currentNode.CUSTOM_WIDTH = ''
        }
        currentNode.children[0].parentItem = {}
        currentNode.children[0].parentItem.COLOR_BLOCK = currentNode.COLOR_BLOCK
      } else {
        delete currentNode.children
      }
    },
    // Поиск блока по дереву
    findNode(id, currentNode, action, color) {
      let i, 
      currentChild, 
      result;
      if (id == currentNode.ID) { 
          return currentNode; 
      } else if(!this.result_found) { 
        if(currentNode.children) {
          for (i = 0; i < currentNode.children.length; i += 1) { 
            currentChild = currentNode.children[i];
            result = this.findNode(id, currentChild, action, color);
            if (result !== false && !this.result_found && !this.searchBlock) {
              this.searchBlock = result
              if(action == 'del') {
                currentNode.children.splice(i, 1);
              }
              this.result_found = true;
              return null; 
            } else if (result !== false && !this.result_found && action == 'add') {
              this.lvl = currentChild.level
              this.lvl++
              
              if(this.searchBlock.level != this.lvl) {
                
                this.takeChild(this.searchBlock, color)
              }
              if(!currentChild.children) {
                currentChild.children = [];
              }
              if(currentChild.level != 5) {
                  currentChild.children.push(this.searchBlock)
              } else {
                if(currentChild.children.length) {
                  if(currentChild.children[0].childs) {
                    currentChild.children[0].childs.push(this.searchBlock)
                  } else {
                    currentChild.children[0].childs = [this.searchBlock]
                  }
                } else {
                  currentChild.children.push({
                    childs: [this.searchBlock]
                  })
                }
              }
              this.result_found = true;
              return null; 
            }
          } 
          return false; 
        } else {
          return false; 
        }
      } 
    },
    /*Drag and Drop*/
    /*startDrag(event, item, parantBlock, mode) {
      console.log(event, item, parantBlock, mode)
      
      // console.log(document.querySelector("#target_block").innerText)
      // console.log(document.querySelector("#target_block").outerHTML)

      if(mode == 'post') {
        
        if(this.editMode) {
          this.$store.commit('setDropMode', mode);
          if (event.getModifierState("Shift")) {
            this.dragMove(event, item, parantBlock, mode)
          }
          // if (event.getModifierState("Control")) {
          //   this.dragCopy(event, item, parantBlock, mode)
          // }
        } else {
          event.dataTransfer.dropEffect = 'none'
          event.dataTransfer.effectAllowed = 'none'
        }
      } else if(mode == 'block') {
        if(this.editMode) {
          this.$store.commit('setDropMode', mode);
          if (event.getModifierState("Shift")) {
            this.dragMove(event, item, parantBlock, mode)
          } else {
            return false
          }
          // if (event.getModifierState("Control")) {
          //   this.dragCopy(event, item, parantBlock, mode)
          // }
        } else {
          event.dataTransfer.dropEffect = 'none'
          event.dataTransfer.effectAllowed = 'none'
        }
      }
      
    },*/
    /*dragCopy(event, item, parantBlock, mode) {
      if(mode == 'post') {
        event.dataTransfer.dropEffect = 'copy'
        event.dataTransfer.effectAllowed = 'copy'
        event.dataTransfer.setData('type', 'copy')
        event.dataTransfer.setData('oldBlockID', parantBlock.ID)
        let post = JSON.stringify(item)
        event.dataTransfer.setData('type', 'copy')
        event.dataTransfer.setData('post', post)
      } else if(mode == 'block') {
        event.dataTransfer.dropEffect = 'move'
        event.dataTransfer.effectAllowed = 'move'
        event.dataTransfer.setData('oldBlockID', parantBlock.ID)
        event.dataTransfer.setData('type', 'copy')
      }
      
    },*/
    move(e) {
      
      console.log(elem.style.top)
    },
    /*dragMove(event, item, parantBlock, mode) {
      if(mode == 'post') {
         // Создаем клон перетаскиваемого поста
        let elem = document.getElementById('post' + item.id)
        let clone = elem.cloneNode(true);
        // Масштабируем клона и назначаем стили
        clone.classList.add('targetBlock')
        clone.style.zoom = this.currentScale
        clone.style.width = 100 * this.currentScale + '%'
        clone.style.height = 100 * this.currentScale + '%'
        clone.style.background = parantBlock.COLOR_BLOCK
        // Даем клону id и засовываем в оригинал
        clone.id = 'clone'
        elem.prepend(clone);
        // Добавляем данные в event
        event.dataTransfer.setDragImage(clone, event.offsetX * this.currentScale , event.offsetY);
        event.dataTransfer.dropEffect = 'move'
        event.dataTransfer.effectAllowed = 'move'
        event.dataTransfer.setData('type', 'move')
        event.dataTransfer.setData('oldBlockID', parantBlock.ID)
        let post = JSON.stringify(item)
        event.dataTransfer.setData('post', post)
      } else if(mode == 'block') {
        // Создаем клон перетаскиваемого блока без панели управления
        let elem = document.getElementById('block' + parantBlock.ID)
        let clone = elem.cloneNode(true);
        let controlBlock = clone.querySelector('.control-block')
        controlBlock.parentNode.removeChild(controlBlock);
        // Масштабируем клона и назначаем стили
        clone.classList.add('targetBlock')
        clone.style.zoom = this.currentScale
        clone.style.width = 100 * this.currentScale + '%'
        clone.style.height = 100 * this.currentScale + '%'
        // Даем клону id и засовываем в оригинал
        elem.prepend(clone);
        clone.id = 'clone'
        // Добавляем данные в event
        event.dataTransfer.setDragImage(clone, event.offsetX * this.currentScale , event.offsetY);
        event.dataTransfer.dropEffect = 'move'
        event.dataTransfer.effectAllowed = 'move'
        event.dataTransfer.setData('oldBlockID', parantBlock.ID)
        event.dataTransfer.setData('type', 'move')
      }
    },*/
    /*dragEnd(event, elem, nodeEmp, mode) {
      if(mode == 'post') {
        this.$store.commit('setDropMode', '');
        this.isDropActive = 0
        this.isDropPostActive = 0
        if (event.dataTransfer.dropEffect == 'move') {
          for(let i = 0; i < nodeEmp.POSTS.length; i++) {
            if(nodeEmp.POSTS[i].id == elem.id) {
              nodeEmp.POSTS.splice(i, 1)
              break
            }
          }
        }
        
      } 
      let element = document.getElementById('clone')
      element.parentNode.removeChild(element);
    },*/
    onDrop(event, data, propsData) {
      console.log('drop in department')
      if(this.editMode && !this.longBlock) {
        /*if (this.mode === 'user') {
          // console.log('event', event, 'data', data, 'propsData', propsData)
            if (event.getModifierState("Shift")) {
              this.dropMove(event, data, propsData)
            }
            // if (event.getModifierState("Control")) {
            //   this.dropCopy(event, data, propsData)
            // }
            this.isDropActive = 0;
            this.isDropPostActive = 0;
            // this.$store.commit('setDropMode', '');
            console.log('onDropFinish', this.mode, this.isDropActive)

        } else if (this.mode === 'post') {

            if (event.getModifierState("Shift")) {
              this.dropMovePost(event, data, propsData)
            }
            // if (event.getModifierState("Control")) {
            //   this.dropCopyPost(event, data, propsData)
            // }
            this.isDropActive = 0;
            this.isDropPostActive = 0;
            // this.$store.commit('setDropMode', '');
            console.log('onDropFinish', this.mode, this.isDropActive)

        } else*/ if (this.mode === 'block') {

            if (event.getModifierState("Shift")) {
              this.dropMoveBlock(event, data, propsData)
            }
            // if (event.getModifierState("Control")) {
            //   this.dropCopyBlock(event, data, propsData)
            // }
            this.isDropActive = 0;
            // this.isDropBlockActive = 0;
            this.$store.commit('setIsDropActiveId', 0);
            this.$store.commit('setIsDropActiveText', '');
            this.$store.commit('setDropMode', '');

        }
      }
      
    },
    /*dropMovePost(event, data, propsData) {
      event.preventDefault();
      // console.log('dropMovePost', data, propsData)
      const post = JSON.parse(event.dataTransfer.getData('post'))
      const oldBlockID = event.dataTransfer.getData('oldBlockID')
      const typeEvent = event.dataTransfer.getData('type')
      // this.addDropElem(propsData, '', post, typeEvent)
      if(propsData.ID != oldBlockID) {
        this.addDropElem(propsData, '', post, typeEvent)
      } else {
        propsData.POSTS.push(post);
      }
    },
    dropCopyPost(event, data, propsData) {
      event.preventDefault();
      const post = JSON.parse(event.dataTransfer.getData('post'))
      const oldBlockID = event.dataTransfer.getData('oldBlockID')
      const typeEvent = event.dataTransfer.getData('type')
      console.log('dropMovePost', propsData.ID, oldBlockID, post)
      if(propsData.ID != oldBlockID) {
        this.addDropElem(propsData, '', post, typeEvent)
        const moveData = {
          typeEvent: typeEvent,
          entityType: 'post',
          newParentId: propsData.ID,
          blockId: oldBlockID
        };
        this.SET_DROP_ELEMENT(moveData);
      } else {
      }
    },*/
    dropMoveBlock(event, data, propsData) {
      event.preventDefault();
      // console.log('dropMoveBlock', data, propsData)
      const oldBlockID = event.dataTransfer.getData('oldBlockID')
      const typeEvent = event.dataTransfer.getData('type')
      if(propsData.ID != oldBlockID) {
        this.addDropElem(propsData, oldBlockID, '', typeEvent)
        const moveData = {
          typeEvent: typeEvent,
          entityType: 'block',
          newParentId: propsData.ID,
          blockId: oldBlockID
        };
        this.SET_DROP_ELEMENT(moveData);
      }
    },
    dropCopyBlock(event, data, propsData) {
      event.preventDefault();
      const oldBlockID = event.dataTransfer.getData('oldBlockID')
      const typeEvent = event.dataTransfer.getData('type')
      // console.log('dropMovePost', propsData.ID, oldBlockID, typeEvent)
      if(propsData.ID != oldBlockID) {
        this.addDropElem(propsData, oldBlockID, '', typeEvent)
      }
    },
    /*dropCopy(event, data, propsData) {
      // data - пост в который передали пользователя 
      // propsData - блок с постами
      event.preventDefault();
      const typeEvent = event.dataTransfer.getData('type')
      const oldPostID = event.dataTransfer.getData('oldPostID')
      const user = JSON.parse(event.dataTransfer.getData('user'))
      
      // typeEvent - скопировали
      // oldPostID - id поста от куда скопировали
      // user - пользователь которого скопировали
      // console.log('typeEvent', typeEvent, 'oldPostID', oldPostID, 'user', user)
      if (data.id != oldPostID ) {
        let objectDrag = {
          typeEvent: typeEvent,
          oldPostId: oldPostID,
          newPostId: data.id,
          user: user
        }
        this.addDropElem(propsData, data.id, objectDrag.user, typeEvent, 'user')
        this.SET_DROP_ELEMENT(objectDrag);
      } else {

      }
    },
    dropMove(event, data, propsData) {
      // data - пост в который передали пользователя 
      // propsData - блок с постами
      // console.log(event.dataTransfer.getData('user'))
      event.preventDefault();
      const typeEvent = event.dataTransfer.getData('type')
      const oldPostID = event.dataTransfer.getData('oldPostID')
      const user = JSON.parse(event.dataTransfer.getData('user'))
      // typeEvent - перетащили
      // oldPostID - id поста от куда перетащили
      // user - пользователь которого перетащили
      // console.log('typeEvent', typeEvent, 'oldPostID', oldPostID, 'user', user)
      if (data.id != oldPostID ) {
        let objectDrag = {
          typeEvent: typeEvent,
          oldPostId: oldPostID,
          newPostId: data.id,
          user: user
        }
        this.addDropElem(propsData, data.id, objectDrag.user, typeEvent)
        this.SET_DROP_ELEMENT(objectDrag);
      } else {
        let objectDrag = {
          typeEvent: typeEvent,
          oldPostId: oldPostID,
          newPostId: oldPostID,
          user: user
        }
        for(let i in propsData.POSTS) {
          if(propsData.POSTS[i].id == oldPostID) {
            propsData.POSTS[i].users.push(user);
            this.SET_DROP_ELEMENT(objectDrag);
          }
        }
      }
    },*/
    addDropElem(container, postId, elem, typeEvent) {
      console.log('addDropElem', container, postId, elem, typeEvent)
      /*if(this.mode == 'user') {
        // console.log('container', container, 'postId', postId, 'elem', elem, 'typeEvent', typeEvent);
        if(typeEvent == 'move') {
          // перебираем все посты в контейнере
          for(let i in container.POSTS) {
            // находим тот в который хотели положить пользователя
            if(container.POSTS[i].id == postId) {
              // проверяем если в посте объект users
              if (container.POSTS[i].hasOwnProperty('users')) {
                // перебираем всех userов
                for (let j in container.POSTS[i].users) {
                  // userов со значение null удаляем
                  if(container.POSTS[i].users[j] == null) {
                    container.POSTS[i].users.splice(j, 1);
                  } 
                }
                let count = 0
                // перебираем всех userов
                for (let j in container.POSTS[i].users) {
                  // если user уже есть увеличиваем счетчик
                  if(container.POSTS[i].users[j].ID == elem.ID) {
                    count++
                  }
                }
                // если elem отсутствует то добавляем
                if(count == 0) {
                  container.POSTS[i].users.push(elem);
                } else {
                  count = 0
                }
                // container.POSTS[i].users.push(elem);
              } else {
                container.POSTS[i].users = [];
                return container.POSTS[i].users.push(elem)
              }
            }
          }
        } else if (typeEvent == 'copy') {
          // перебираем все посты в контейнере
          for(let i in container.POSTS) {
            // находим тот в который хотели положить пользователя
            if(container.POSTS[i].id == postId) {
              // проверяем если в посте объект users
              if (container.POSTS[i].hasOwnProperty('users')) {
                let count = 0
                // перебираем всех userов
                for (let j in container.POSTS[i].users) {
                  // если elem уже есть увеличиваем счетчик
                  if(container.POSTS[i].users[j].ID == elem.ID) {
                    count++
                  }
                }
                // если elem отсутствует то добавляем
                if(count == 0) {
                  container.POSTS[i].users.push(elem);
                } else {
                  count = 0
                }
              // так как users отсутствует создаем массив и добавляем туда user
              } else {
                container.POSTS[i].users = [];
                return container.POSTS[i].users.push(elem)
              }
            }
          }
        }
        // container - блок с постами
        // postId - пост в который перетащили
        // user - пользователь которого перетащили
      } else if(this.mode == 'post') {
        // console.log(container, elem, this.mode, typeEvent)
        if(typeEvent == 'move') {
          if (container.POSTS) {
            console.log(container.POSTS)
            let count = 0
            // перебираем все посты в контейнере
            for(let i in container.POSTS) {
              // если elem уже есть увеличиваем счетчик
              if(container.POSTS[i].id == elem.id) {
                count++
              }
            }
            // если elem отсутствует то добавляем
            console.log('добавляем')
            if(count == 0) {
              container.POSTS.push(elem);
              
            } else {
              count = 0
            }
          } else {
            console.log(container.POSTS)
            container.POSTS = [];
            return container.POSTS.push(elem)
          }
        } else if(typeEvent == 'copy') {
          // проверяем есть ли объект POSTS
          if (container.POSTS) {
            let count = 0
            // перебираем все посты в контейнере
            for(let i in container.POSTS) {
              // проверяем есть ли пост в контейнере
              if(container.POSTS[i].id == elem.id) {
                count++
              }
            }
            // если elem отсутствует то добавляем
            if(count == 0) {
              container.POSTS.push(elem);
            } else {
              count = 0
            }
          } else {
            container.POSTS = [];
            return container.POSTS.push(elem)
          }
        }
      } else*/ if(this.mode == 'block') {
        // console.log('addDropElem', container, Number(postId), this.mode, typeEvent, this.departmentData)
        // container - 'элемент в который передаем или из которого удаляем'; Number(postId) - id элемента которого удаляем/добавляем
        if(typeEvent == 'move') {
          console.log(container)
          this.find(Number(postId), container.ID, this.departmentData, 'del', container.COLOR_BLOCK)
        } else if(typeEvent == 'copy') {
          this.find(Number(postId), container.ID, this.departmentData, 'add')
        }
      } 
      
      
    },
    onDragEnter(event, postData, index) {
      console.log('dragEnter', event, postData, index)
      /*if(this.mode == 'user') {
        if (event.getModifierState("Shift")) {
          if(this.editMode) {
            this.isDropActive = postData.id;
            
              if(event.getModifierState("Shift")) {
                // this.dropAreaText = 'Переместить сотрудника';
                this.$store.commit('setIsDropActiveText', 'Переместить сотрудника');
              }
              // if(event.getModifierState("Control")) {
              //   this.dropAreaText = 'Скопировать сотрудника';
              // }
          }
        } 
      } else if(this.mode == 'post') {
        if (event.getModifierState("Shift")) {
          if(this.editMode) {
            this.isDropActive = postData.id;
            this.isDropPostActive = this.nodeEmp.ID
            if(event.getModifierState("Shift")) {
              // this.dropAreaText = 'Поместить пост';
              this.$store.commit('setIsDropActiveText', 'Поместить пост');
            }
            // if(event.getModifierState("Control")) {
            //   this.dropAreaText = 'Скопировать пост';
            // }
          }
        } 
      } else*/ if(this.mode == 'block' && !this.longBlock) {
        if (event.getModifierState("Shift")) {
          if(this.editMode) {
            this.$store.commit('setIsDropActiveId', this.nodeEmp.ID);
            // this.isDropBlockActive = this.nodeEmp.ID
            console.log(this.isDropBlockActive, this.nodeEmp.ID)
            if(event.getModifierState("Shift")) {
              // this.dropAreaText = 'Поместить блок';
              this.$store.commit('setIsDropActiveText', 'Поместить блок');
            }
            // if(event.getModifierState("Control")) {
            //   this.dropAreaText = 'Скопировать блок';
            // }
          }
        } 
      }
    },
    onDragLeave(event, postData) {
      if(this.editMode && !this.longBlock) {
        // console.log('onDragLeave', event.fromElement.className)
        /*if(this.mode == 'user') {
          console.log(event)
          if(!event.fromElement.classList.contains('droppable') &&
             !event.fromElement.classList.contains('preview') &&
             !event.fromElement.classList.contains('title') &&
             !event.fromElement.classList.contains('droppable-overlay-user') &&
             !event.fromElement.classList.contains('droppable-overlay-title-user') &&
             !event.fromElement.classList.contains('footer')) {
            this.isDropActive = 0;
          } 
        } else if(this.mode == 'post') {
          if(!event.fromElement.classList.contains('droppable-overlay') && 
             !event.fromElement.classList.contains('droppable-overlay-title') && 
             !event.fromElement.offsetParent.classList.contains('postinfo') && 
             !event.fromElement.classList.contains('footer') && 
             !event.fromElement.classList.contains('open-link')) {
            this.isDropActive = 0;
            this.isDropPostActive = 0;
          } 
        } else*/ if(this.mode == 'block') {
          if(event.getModifierState("Shift")) {
            if(!event.fromElement.classList.contains('droppable-overlay') && 
              //  !event.fromElement.classList.contains('mainDep') && 
              !event.fromElement.classList.contains('droppable-overlay-title')) {
              this.isDropActive = 0;
              // this.isDropBlockActive = 0;
              this.$store.commit('setIsDropActiveId', 0);
              this.$store.commit('setIsDropActiveText', '');
            } 
          }
          
        }
      }
    }
  },
  watch: {},
  components: { Icon, UserList, /*CardSection, */CardBlock },
  props: ["nodeEmp","treeNode","hideCkp","hasChildren"],
};
</script>

<style scoped lang="scss">
* {
  box-sizing: border-box;
}

.targetBlock {
  position: absolute !important;
  width: 100%;
  top: 0;
  left: 0px;
  // border: 1px solid black;
  // background: blue;
  z-index: -1;
}
.clearBlock {
  opacity: 0;
  position: absolute;
}
.container{
  text-align: center;
  width: 580px;
  z-index: 1;
}
.posStat {
  position: static;
}

.mainCardDep {
  position: absolute;
  z-index: 9;
  width: 100%;
  top: 25px;
  height: calc(100% - 75px);
  .droppable-overlay {
    border-radius: 8px;
    border: 2px dashed #f5f5f5;
    background: rgba(0, 0, 0, 0.2);
    width: 100%;
    height: 100%;
    position:absolute;
    top: 0;
    left: 0;
    z-index: 1;
    .droppable-overlay-title {
      position: absolute;
      text-align: center;
      color: #f5f5f5;
      font-size: 36px;
      font-weight: 400;
      top: calc(50% - 20px);
      width: 100%;
      z-index: 1;
    }
  }
}
.container {
  /*max-width: 160px;*/
  padding: 0 10px;
  margin: 0 auto 50px;
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  justify-content: center;
  border-radius: 4px;
  width: 300px;
  position: relative;

  
  &.long {
    height: 100%;

    /*.section {
      height: 100%;
    }*/
  }
  
  .bottom-line {
    position: absolute;
    width: 2px;
    height: 25px;
    bottom: -25px;
    background: #cecece;
    left: calc(50% - 1px);
  }
}
.mainDep {
  position: relative;
  z-index: 1;
}
.parent-block {
  // &.lv4 > .mainDep > .container > .mainCardDep {
  //   position: absolute;
  //   width: 100%;
  //   height: 100%;
  // }
  &.lv1 > .mainDep > .container {
    text-align: center;
    width: 580px;
    .section {
      min-height: 155px;

      .postinfo > .functions {
        display: none;
      }
    }
  } 
  &.lv2 > .mainDep > .container {
    text-align: center;
    width: 580px;
    .section {
      min-height: 155px;

      .postinfo > .functions {
        display: none;
      }
    }
  } 
  &.lv3 > .mainDep > .container {
    text-align: center;
    width: 580px;
    .section {
      min-height: 200px;

      .postinfo > .functions {
        display: none;
      }
    }
  } 
  &.lv4 > .mainDep > .container {
    text-align: center;
    width: 580px;
    .section {
      min-height: 200px;

      .postinfo > .functions {
        display: none;
      }
    }
  } 
  &.lv5 > .mainDep > .container {
    .section {
      .droppablePost {
        height: calc(100% - 33px);
        position: absolute;
        width: 100%;
      }
      // .droppable-overlay {
      //   border-radius: 8px;
      //   border: 2px dashed #f5f5f5;
      //   background: rgba(0, 0, 0, 0.2);
      //   width: 100%;
      //   position:absolute;
      //   top: 0;
      //   left: 0;
      //   height: calc(100% - 33px);
      //   margin-top: 33px;
      //   .droppable-overlay-title {
      //     position: absolute;
      //     text-align: center;
      //     color: #f5f5f5;
      //     font-size: 26px;
      //     font-weight: 400;
      //     top: calc(50% - 15px);
      //     width: 100%;
      //   }
      // }
      .header {
        height: 33px;
        font-size: 10px;
        letter-spacing: 0.1em;
        vertical-align: center;
      }
      .postinfo > .functions {
        display: none;
      }
    }
  }
  &.lv6 > .mainDep {
    display: contents;
  }
}

.section {
  list-style-type: none;
  flex: 1 1 auto;
  min-height: 270px;
  height: 100%;
  margin: 0;
  padding: 0;
  -webkit-transition: 0.3s;
  transition: 0.15s;
  border-radius: 4px;
  /*overflow: hidden;*/
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
  background: #ffffff;
  position: relative;
  border: 1px solid #d4d4d4;

  // div {
    .droppablePost {
      height: calc(100% - 25px);
      position: absolute;
      width: 100%;
    }
    .postinfoPostEmpty {
      min-height: 75px;
      width: 100%;
    }
    .droppable-overlay {
      border-radius: 8px;
      border: 2px dashed #f5f5f5;
      background: rgba(0, 0, 0, 0.2);
      width: 100%;
      position:absolute;
      top: 0;
      left: 0;
      height: 100%;
      // margin-top: 25px;
      .droppable-overlay-title {
        position: absolute;
        text-align: center;
        color: #f5f5f5;
        font-size: 26px;
        font-weight: 400;
        top: calc(50% - 15px);
        width: 100%;
      }
    }
    .postinfo {
      padding: 0 10px;
      color: #131313;
      position: relative;

      .title {
        padding: 10px 0;

        a {
          display: block;
          color: #131313;
          text-decoration: none;
          font-family: OpenSans-Regular, "Helvetica Neue", Arial, Helvetica, sans-serif;
          font-size: 14px;
        }
      }
      .droppable-overlay-user {
        background: rgba(0, 0, 0, 0.2);
        width: 100%;
        height: 100%;
        position:absolute;
        top: 0;
        left: 0;
        border: 2px dashed #f5f5f5;
        border-radius: 8px;
        .droppable-overlay-title-user {
          position: absolute;
          text-align: center;
          color: #f5f5f5;
          font-size: 16px;
          font-weight: 400;
          top: calc(50% - 10px);
          width: 100%;
        }
      }
      .droppable {
        

        div {
          pointer-events: none;
        }

        
      }

      // &.droppablePost {
      //   border: 2px dashed #f5f5f5;
      //   border-radius: 8px;
      //   position: relative;
      //   height: calc(100% - 25px);
      //   div {
      //     pointer-events: none;
      //   }
      .droppable-overlay {
        border-radius: 8px;
        border: 2px dashed #f5f5f5;
        background: rgba(0, 0, 0, 0.2);
        width: 100%;
        height: 100%;
        position:absolute;
        top: 0;
        left: 0;
        .droppable-overlay-title {
          position: absolute;
          text-align: center;
          color: #f5f5f5;
          font-size: 26px;
          font-weight: 400;
          top: calc(50% - 15px);
          width: 100%;
        }
      }
      // }
    }
    
    .functions {
      font-family: OpenSans-Regular,"Helvetica Neue", Arial, Helvetica, sans-serif;
      font-size: 9px;

      .title {
        color: #131313;
        font-size: 12px;
        font-weight: bold;
      }
      .content {
        word-break: break-word;
        text-transform: uppercase;
        font-weight: bold;
      }
    }
  // }
  
  &:hover {
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);

    .control-block {
      display: flex;
      align-items: center;
      justify-content: flex-end;
    }
  }

  &.wide {
    width: 300px;
  }

  &.hide {
    box-shadow: none;
    background: none;
    border: none;

    .line {
      position: absolute;
      width: 2px;
      height: 100%;
      top: 0;
      left: calc(50% - 1px);
      background: #cecece;
    }
  }

  .control-block {
    position: absolute;
    width: 100%;
    top: 0;
    left: 0;
    height: 25px;
    display: none;
    background: rgba(0,0,0,0.85);
  }

  .header {
    background: #312F3F;
    text-transform: uppercase;
    font-weight: bold;
    height: 25px;
    padding: 4px 8px 10px 8px;
    font-size: 14px;
    color: #ffffff;
    font-family: OpenSans-Semibold, "Helvetica Neue", Arial, Helvetica, sans-serif;
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
  }

  

  

  .long-container {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
  }

  .bottom-block {
    padding: 10px;

    .functions {
      padding: 10px;
      min-height: 200px;
      border-radius: 8px;
      border: 1px solid #dddddd;
    }
  }
}

.department-ckp-block {
  position: absolute;
  width: 0;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
  background: #ffffff;
  border-radius: 4px;
  padding: 10px;
  min-height: 60px;

  .border {
    border: 1px solid #dddddd;
    border-radius: 8px;
    padding: 10px;
  }
}
</style>