<?php if (!empty($error)) : ?></php>
    <div class="alert alert-danger" role="alert">
        <?= $error ?>
    </div>
<?php endif; ?>

<div class="container h-100 justify-content-center align-items-center p-0">
    <div class="col border rounded p-4 text-center">
        <h3 class="text-center mb-4 fa fa-bars"> Tree</h3>
    </div>

    <section class="section-no-border">
        <section>
            <div class="bg-white border rounded-5">
                <section class="section-preview">

                    <div class="treeview-animated w-20 my-4">
                        <ul class="treeview-animated-list mb-3 list-group-numbered">
                            <?php if (empty($tree)) : ?>
                                <li class="treeview-animated-items">
                                    <div class="closed">
                                        <i class="fas fa-angle-right"></i>
                                        <span id="0"><i class="far fa-folder ic-w me-2"></i>Root</span>
                                    </div>
                                    <div class="control_panel">
                                        <a id="0" href="/main/edit/?rename=0" class="rename_field_modal fa fa-edit" title="Переименовать"></a>
                                        ||
                                        <a id="0" href="/main/edit/?add=0" class="add_field_modal add_field fa fa-add" title="Добавить"></a>
                                    </div>
                                </li>
                            <?php else : ?>
                                <?= $tree ?>
                            <?php endif; ?>
                        </ul>
                    </div>

                </section>
            </div>
        </section>
    </section>
</div>

<script type="text/javascript" src="/Public/js/main.js"></script>