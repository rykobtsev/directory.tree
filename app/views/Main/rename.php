<div class="container mt-5">
    <h2>Переименование</h2>
    <form id="renameForm" action="/main/rename" method="POST">
        <div class="form-group">
            <label for="name">Введите новое название:</label>
            <input type="text" id="newNameInput" name="name" required value="<?= $name ?? '' ?>">
        </div>
        <input type="hidden" id="itemId" name="id" value="<?= $id ?? 0 ?>">
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <button type="button" class="btn btn-secondary" onclick="window.location.href = '/'">Назад</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $("#renameForm").on("submit", function(event) {
            event.preventDefault();
            const form = $(this);

            $.ajax({
                url: form.attr("action"),
                method: form.attr("method"),
                data: form.serialize(),
                success: function(response) {
                    window.location.href = "/";
                },
                error: function() {
                    alert("Произошла ошибка при обращении к серверу.");
                },
            });
        });
    });
</script>