<template>
  <div class="icon-bar">
    <a href="#" @click.stop="addBox"><i class="fa fa-plus"></i></a>
    <a href="#" @click.stop="editBox"><i class="fa fa-edit"></i></a>
    <a href="#" @click.stop="removeBox"><i class="fa fa-trash"></i></a>
  </div>
</template>

<script>
export default {
  name: "Icon",
  data() {
    return {};
  },
  methods: {
    editBox() {
      console.log("edit");
    },
    removeBox() {
      console.log("delete");
    },
    addBox() {
      console.log("add");
    },
  },
};
</script>

<style scoped>
body {
  margin: 0;
}

.icon-bar {
  width: 100%;
  background-color: #555;
  overflow: auto;
}

.icon-bar a {
  float: left;
  width: 33%;
  text-align: center;
  padding: 8px 0;
  transition: all 0.3s ease;
  color: white;
  font-size: 14px;
}

.icon-bar a:hover {
  background-color: #000;
}

.active {
  background-color: #4CAF50 !important;
}
</style>
