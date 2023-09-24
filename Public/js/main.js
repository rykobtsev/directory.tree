function applyScripts() {
  $(".treeview-animated").mdbTreeview();

  $("div.closed").on("click", function () {
    $(this)
      .find("i.fa-folder, i.fa-folder-open")
      .toggleClass("fa-folder fa-folder-open");
  });

  $("li")
    .has("div.open")
    .each(function () {
      var innerUl = $(this).find("ul");
      innerUl.removeAttr("style");

      innerUl.addClass("active");
    });
}

$(document).ready(function () {
  applyScripts();

  $(".container").append(`
    <div class="modal fade bootstrapModal" id="modalEditWindow" tabindex="-1" aria-labelledby="renameModalLabel" aria-hidden="true" data-backdrop="static"></div>
  `);

  const modalRenameHtml = `
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="renameModalLabel">Переименование</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Введите новое название:</p>
          <input type="text" id="newNameInput" class="form-control">
        </div>
        <div class="modal-footer">
          <div class="badge bg-secondary badge-lg" id="timer" style="font-size: 24px; margin-right: 30px;">20</div>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Нет</button>
          <button type="button" class="btn btn-primary" id="renameButton">Да</button>
        </div>
      </div>
    </div>
  `;

  const modalAddHtml = `
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addModalLabel">Создание</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Введите название:</p>
          <input type="text" id="nameInput" class="form-control">
        </div>
        <div class="modal-footer">
          <div class="badge bg-secondary badge-lg" id="timer" style="font-size: 24px; margin-right: 30px;">20</div>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Нет</button>
          <button type="button" class="btn btn-primary" id="addButton">Да</button>
        </div>
      </div>
    </div>
  `;

  const modalDeleteHtml = `
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Подтверждение удаления</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Вы уверены, что хотите удалить эту запись?
        </div>
        <div class="modal-footer">
          <div class="badge bg-secondary badge-lg" id="timer" style="font-size: 24px; margin-right: 30px;">20</div>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Нет</button>
          <button type="button" class="btn btn-danger" id="confirmDelete">Да</button>
        </div>
      </div>
    </div>
  `;

  let selectedItem;
  let timer;
  const countdown = 20;

  function startTimer() {
    let seconds = countdown;
    timer = setInterval(function () {
      $("#timer").text(seconds);
      seconds--;
      if (seconds < 0) {
        clearInterval(timer);
        $(".bootstrapModal").modal("hide");
      }
    }, 1000);
  }

  $(".bootstrapModal").on("show.bs.modal", function () {
    clearInterval(timer);
    startTimer();
  });

  $(".bootstrapModal").on("hidden.bs.modal", function () {
    clearInterval(timer);
    $("#modalEditWindow").html("");
  });

  $(".container").on("click", ".rename_field_modal", function (event) {
    selectedItem = $(this);
    $("#modalEditWindow").html(modalRenameHtml);
    startTimer();
    event.preventDefault();
    $("#modalEditWindow").modal("show");
  });

  $("#modalEditWindow").on("click", "#renameButton", function () {
    clearInterval(timer);
    var name = $("#newNameInput").val();
    if (name !== "") {
      var id = selectedItem.attr("id");

      $.ajax({
        url: "/main/rename",
        method: "POST",
        data: {
          id: id,
          name: name,
        },
        success: function (response) {
          var parsedResponse = JSON.parse(response);
          var id = parsedResponse.id;
          var name = parsedResponse.name;

          $(`#${id}`).text(name);
        },
        error: function () {
          alert("Произошла ошибка при обращении к серверу.");
        },
      });
    }
    $(".bootstrapModal").modal("hide");
  });

  $(".container").on("click", ".add_field_modal", function (event) {
    selectedItem = $(this);
    $("#modalEditWindow").html(modalAddHtml);
    startTimer();
    event.preventDefault();
    $("#modalEditWindow").modal("show");
  });

  $("#modalEditWindow").on("click", "#addButton", function () {
    clearInterval(timer);
    const name = $("#nameInput").val();
    if (name !== "") {
      var pid = selectedItem.attr("id");

      $.ajax({
        url: "/main/add",
        method: "POST",
        data: {
          pid: pid,
          name: name,
          ajaxRequest: true,
        },
        success: function (response) {
          var parsedResponse = JSON.parse(response);
          var id = parsedResponse.id;
          var pid = parsedResponse.pid;

          var controlPanel = `
            <div class="control_panel">
              <a href="#" id="${id}" class="rename_field_modal fa fa-edit" title="Переименовать"></a> 
              ||
              <a href="#" id="${id}" class="add_field_modal fa fa-add" title="Добавить"></a> 
              ||
              <a href="#" id="${id}" class="delete_field fa fa-remove" title="Удалить"></a>
            </div>
          `;

          var liChild = `
            <li>
              <div id="${id}" class= "treeview-animated-element me-2">
                <i class="far fa-file-text ic-w me-2"></i>
                ${name}
              </div> 
              ${controlPanel}
            </li>
          `;

          const parentElement = $(`ul.parent-${pid}`);

          if (parentElement.is("ul")) {
            parentElement.append(liChild);
          } else if ($(`div#${pid}`).is("div")) {
            const parentName = $(`div#${pid}`).text();
            $(`div#${pid}`)
              .parent()
              .addClass("treeview-animated-items")
              .append(
                `<ul class="nested list-group-numbered parent-${pid}">
                  ${liChild}
                </ul>`
              );
            $(`div#${pid}`).replaceWith(
              `<div class="closed open">
                  <i class="fas fa-angle-right down"></i>
                  <span id="${pid}">
                    <i class="far fa-folder-open ic-w me-2"></i>
                    ${parentName}
                  </span>
                </div>`
            );
          } else {
            const liElement = $("li").find("span#0").closest("li");
            const rootName = $("span#0").text();

            liElement.html(
              `<div class="closed open">
                  <i class="fas fa-angle-right down"></i>
                  <span id="${pid}">
                    <i class="far fa-folder-open ic-w me-2"></i>
                    ${rootName}
                  </span>
                </div>
                <div class="control_panel">
                  <a href="#" id="${pid}" class="rename_field_modal fa fa-edit" title="Переименовать"></a> 
                  ||
                  <a href="#" id="${pid}" class="add_field_modal fa fa-add" title="Добавить"></a> 
                  ||
                  <a href="#" id="${pid}" class="delete_field fa fa-remove" title="Удалить"></a>
                </div>
                <ul class="nested list-group-numbered parent-${pid}">
                  ${liChild}
                </ul>`
            );
          }

          applyScripts();
        },
        error: function () {
          alert("Произошла ошибка при обращении к серверу.");
        },
      });
    }
    $(".bootstrapModal").modal("hide");
  });

  $(".container").on("click", ".delete_field", function (event) {
    event.preventDefault();
    var id = $(this).attr("id");

    $.ajax({
      url: "/main/delete",
      method: "POST",
      data: {
        id: id,
      },
      success: function (response) {
        var parsedResponse = JSON.parse(response);
        var id = parsedResponse.id;
        $(`#${id}`).closest("li").remove();

        if ($("ul.treeview-animated-list").children().length === 0) {
          location.reload();
        }
      },
      error: function () {
        alert("Произошла ошибка при обращении к серверу.");
      },
    });
  });

  $(".container").on("click", ".delete_field_modal", function (event) {
    selectedItem = $(this);
    $("#modalEditWindow").html(modalDeleteHtml);
    startTimer();
    event.preventDefault();
    $("#modalEditWindow").modal("show");
  });

  $("#modalEditWindow").on("click", "#confirmDelete", function () {
    clearInterval(timer);
    const id = selectedItem.attr("id");

    if ($.isNumeric(id)) {
      $.ajax({
        url: "/main/delete",
        method: "POST",
        data: {
          id: id,
        },
        success: function (response) {
          var parsedResponse = JSON.parse(response);
          var id = parsedResponse.id;
          $(`#${id}`).closest("li").remove();

          if ($("ul.treeview-animated-list").children().length === 0) {
            location.reload();
          }
        },
        error: function () {
          alert("Произошла ошибка при обращении к серверу.");
        },
      });
    }
    $(".bootstrapModal").modal("hide");
  });
});
