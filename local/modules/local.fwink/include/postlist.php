<?php


?>
<script
        src="https://code.jquery.com/jquery-2.2.4.min.js"
        integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
        crossorigin="anonymous"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css"
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.1/bootstrap-table.min.css">
<script type='text/javascript'
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.1/bootstrap-table.min.js"></script>
<style type='text/css'>
    .row-index {
        width: 50px;
        display: inline-block;
    }
</style>

<script type='text/javascript'>
    $(window).load(function () {
        var data = [{
            "name": "bootstrap-table",
            "commits": "10",
            "attention": "122",
            "uneven": "An extended Bootstrap table"
        },
            {
                "name": "multiple-select",
                "commits": "288",
                "attention": "20",
                "uneven": "A jQuery plugin"
            }, {
                "name": "Testing",
                "commits": "340",
                "attention": "20",
                "uneven": "For test"
            }];

        var columns = [
            {
                "field": "name",
                "title": "name",
                "sortable": "True",
            },
            {
                "field": "commits",
                "title": "commits",
                "sortable": "True",
            },
            {
                "field": "attention",
                "title": "attention",
                "sortable": "True",
            },
            {
                "field": "uneven",
                "title": "uneven",
                "sortable": "True",
            }
        ];

        function imageFormatter(value, row, index) {

            return '<img src="https://dummyimage.com/300x${height}/000/fff.png">'
        }


        $(function () {
            $('#table').bootstrapTable({
                data: data,
                columns: columns,
            });

        });

    });
</script>
<div class="container" style="padding: 10px; ">
    <h1>{{title}}</h1>
    <br/>
    <div id="toolbar"></div>
    <table
            id="table"
            data-toggle="true"
            data-toolbar="#toolbar"
            data-search="true"
            data-show-columns="true"
            data-pagination="true"
            data-height="auto">
    </table>
</div>
