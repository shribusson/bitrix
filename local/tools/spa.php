<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Задачи (SPA-интерфейс Bitrix24)</title>
  <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <!-- Замените путь ниже на свой, если нужно -->
  <script src="/bitrix/js/main/core/core.js"></script>
  <script src="/bitrix/js/rest/client/rest.client.js"></script>
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1em;
    }
    th, td {
      padding: 6px;
      border: 1px solid #ccc;
    }
    th {
      background-color: #f7f7f7;
      text-align: left;
    }
  </style>
</head>
<body>
  <div id="app">
    <h2>Задачи (SPA-интерфейс Bitrix24)</h2>
    <div v-if="loading">Загрузка задач...</div>
    <table v-else>
      <thead>
        <tr>
          <th>ID</th>
          <th>Название</th>
          <th>Ответственный</th>
          <th>Постановщик</th>
          <th>Статус</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="task in tasks" :key="task.ID">
          <td>{{ task.ID }}</td>
          <td>{{ task.TITLE }}</td>
          <td>{{ task.RESPONSIBLE_ID }}</td>
          <td>{{ task.CREATED_BY }}</td>
          <td>{{ task.STATUS }}</td>
        </tr>
        <tr v-if="tasks.length === 0">
          <td colspan="5">Задачи не найдены</td>
        </tr>
      </tbody>
    </table>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      new Vue({
        el: '#app',
        data: {
          tasks: [],
          loading: true
        },
        mounted() {
          this.fetchTasks();
        },
        methods: {
          fetchTasks(start = 0, acc = []) {
            if (typeof BX === 'undefined' || !BX.rest) {
              console.error("BX.rest не доступен");
              this.loading = false;
              return;
            }

            BX.rest.callMethod('tasks.task.list', {
              filter: {},
              select: ['ID', 'TITLE', 'RESPONSIBLE_ID', 'CREATED_BY', 'STATUS'],
              start
            }, (res) => {
              if (res.error()) {
                console.error(res.error());
                this.loading = false;
                return;
              }
              acc.push(...res.answer.result.tasks.map(t => t.task));
              if (res.answer.next) {
                this.fetchTasks(res.answer.next, acc);
              } else {
                this.tasks = acc;
                this.loading = false;
              }
            });
          }
        }
      });
    });
  </script>
</body>
</html>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>

