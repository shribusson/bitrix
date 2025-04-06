import axios from "axios";
import Vue from "vue";
import Vuex from "vuex";
import changeData from "./changeData";

Vue.use(Vuex);
const store = new Vuex.Store({
    state: {
        departmentData: {},
        loading: false,
        sign: '',
        signEdit: '',
        editMode: false,
        // userDrag: false,
        mode: '',
        startX: 0,
        startY: 0,
        currentScale: 0,
        isDropActiveId: 0,
        dropAreaText: '',
    },
    mutations: {
        SET_DEPARTMENTDATA_TO_STATE: (state, departmentData) => {
            console.log('commit state');
            state.departmentData = changeData(departmentData);
            console.log(state.departmentData);
        },
        enableLoading: (state) => {
            state.loading = true;
        },
        disableLoading: (state) => {
            state.loading = false;
        },
        setSign: (state, payload) => {
            state.sign = payload.sign;
            state.signEdit = payload.signEdit;
        },
        setEditMode: (state, payload) => {
            state.editMode = payload;
        },
        // setUserDrag: (state, payload) => {
        //     state.userDrag = payload;
        // },
        setDropMode: (state, mode) => {
            state.mode = mode
            console.log(mode)
        },
        setStartXY: (state, mode) => {
            state.startX = mode.startX
            state.startY = mode.startY
        },
        setCurrentScale: (state, mode) => {
            state.currentScale = mode
        },
        setIsDropActiveId: (state, id) => {
            console.log('set is drop active ', id);
            state.isDropActiveId = id
        },
        setIsDropActiveText: (state, text) => {
            state.dropAreaText = text
        },
    },
    actions: {
        GET_STRUCTURE_API({ commit, state }, sign) {
            console.log('start get');
            commit('enableLoading');
            BX.ajax.runAction('local:fwink.api.block.getList', {
                data: {
                    sign: sign || state.sign
                }
            }).then((departmentData) => {
                console.log('response success')
                commit("SET_DEPARTMENTDATA_TO_STATE", departmentData.data); //richMediaData.data
                commit('disableLoading');
                return departmentData;
            });
        },
        SET_BLOCK_WIDTH({ commit, state }, params) {
            BX.ajax.runAction('local:fwink.api.block.setWidth', {
                data: {
                    sign: state.sign,
                    id: params.id,
                    width: params.width
                }
            }).then((response) => {
                //
            });
        },
        SET_DROP_ELEMENT( { commit, dispatch, state }, data ) {
            console.log('SET_DROP_ELEMENT');
            console.log(data);
            if('entityType' in data) {
                let action = '';
                switch(data.entityType) {
                    case 'block':
                        commit('enableLoading');
                        action = 'local:fwink.api.block.move';
                        break;
                    case 'post':
                        action = 'local:fwink.api.post.move';
                        break;
                }
                if(action.length) {
                    BX.ajax.runAction(action, {
                        data: {
                            sign: state.sign,
                            data: data
                        }
                    }).then((response) => {
                        commit('disableLoading');
                        dispatch('GET_STRUCTURE_API');
                    });
                }
            } else {
                // todo: добавить entityType везде в дропе пользователей
                BX.ajax.runAction('local:fwink.api.staff.changePost', {
                    data: {
                        sign: state.sign,
                        data: data
                    }
                }).then((response) => {
                    //
                });
            }
        }
    },
    getters: {
        departmentData(state) {
            console.log('get state');
            console.log(state.departmentData);
            return state.departmentData;
        },
        loading(state) {
            return state.loading
        },
        ajaxSign(state) {
            return state.sign;
        },
        ajaxSignEdit(state) {
            return state.signEdit;
        },
        editMode(state) {
            return state.editMode;
        },
        userDrag(state) {
            return state.userDrag;
        }
    }
});

export default store;