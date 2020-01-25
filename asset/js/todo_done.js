$(function() {
  "use strict";

  /*
    モーダルウインドウ(todo/index.php)
  */
  $(".modal_btn").click(function() {
    $(".modal").addClass("is_modal_active");
    $(".modal_contents").addClass("is_modal_contents_active");
  });

  // モーダルのウインドウの背景をクリックした時
  $(".modal").click(function(event) {
    if (
      !$(event.target).closest(".modal_contents").length &&
      $(".modal").hasClass("is_modal_active")
    ) {
      $(".modal").removeClass("is_modal_active");
      $(".modal_contents").removeClass("is_modal_contents_active");
    }
  });

  // モーダルウインドウのキャンセルボタンをクリックした時
  $(".close_box").click(function() {
    $(".modal").removeClass("is_modal_active");
    $(".modal_contents").removeClass("is_modal_contents_active");
  });

  /*
    Ajax処理
  */
  // Todoリストのチェック(todosテーブルのstateカラムの値を1か0に変更する)todo/index.php
  $(".todos").on("click", ".check_update", function() {
    // idを取得
    let id = $(this)
      .parents(".todo_row")
      .data("id");
    $.post(
      "../lib/_ajax.php",
      {
        id: id,
        mode: "update",
        token: $(".token").val()
      },
      function(res) {
        if (res.state === "1") {
          $(".todo_" + id)
            .find(".todo_title")
            .addClass("todo_finish");
        } else {
          $(".todo_" + id)
            .find(".todo_title")
            .removeClass("todo_finish");
        }
      }
    );
  });

  // todoリストの削除(todo/index.php)
  $(".todos").on("click", ".delete_btn", function() {
    // idを取得
    let id = $(this)
      .parents(".todo_row")
      .data("id");
    if (confirm("本当に削除しますか?")) {
      $.post(
        "../lib/_ajax.php",
        {
          id: id,
          mode: "todo_delete",
          token: $(".token").val()
        },
        function() {
          $(".todo_" + id).fadeOut(10);
        }
      );
    }
  });

  // doneリストの削除(todo/index.php)
  $(".dones").on("click", ".delete_btn", function() {
    // idを取得
    let id = $(this)
      .parents(".done_row")
      .data("id");
    if (confirm("本当に削除しますか?")) {
      $.post(
        "../lib/_ajax.php",
        {
          id: id,
          mode: "done_delete",
          token: $(".token").val()
        },
        function() {
          $(".done_" + id).fadeOut(10);
        }
      );
    }
  });

  // memoのdelete(memo.php)
  $(".ed_de_box").on("click", ".delete_btn", function() {
    // memoのidを取得
    let memo_id = $(this)
      .parents(".ed_de_box")
      .data("id");
    if (confirm("本当に削除しますか？")) {
      $.post(
        "../lib/_ajax.php",
        {
          memo_id: memo_id,
          mode: "memo_delete",
          token: $(".token").val()
        },
        function() {
          $(".memo_" + memo_id).fadeOut(10);
        }
      );
    }
  });
});
