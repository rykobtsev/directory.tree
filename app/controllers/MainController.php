<?php

namespace app\controllers;

use app\models\Main;

class MainController extends AppController
{
    public $layout = 'main';
    public $view = 'index';
    public $error;

    public function indexAction()
    {
        $model = new Main;
        $tree = $this->buildTreeHtml($model->treeAll());
        $this->set(compact('tree'));
    }

    public function editAction()
    {
        $model = new Main;

        $id = $this->postParams['rename'] ?? $this->getParams['rename'] ?? null;

        if (isset($id) && is_numeric($id)) {
            $this->view = 'rename';
            $name = $model->getName($id);
            $this->set(compact('id', 'name'));
        } else {
            $id = $this->postParams['add'] ?? $this->getParams['add'] ?? null;
            if (isset($id) && is_numeric($id)) {
                $this->view = 'add';
                $name = $model->getName($id);
                $this->set(compact('id', 'name'));
            }
        }
    }

    public function renameAction()
    {
        $this->layout = false;
        $model = new Main;

        $id = $this->postParams['id'] ?? $this->getParams['id'] ?? null;
        $name = $this->postParams['name'] ?? $this->getParams['name'] ?? 'Root';

        if (isset($id) && is_numeric($id) && $id != 0) {
            if (!$model->renameBranch($id, $name)) {
                $this->error = "Не удалось переименовать запись (id: $id, name: $name)";
            } else {
                if ($this->isAjax) {
                    $this->set([
                        'id' => $id,
                        'name' => $name
                    ]);
                }
            }
        } elseif (isset($id) && $id == 0) {
            $response = $model->addBranch($id, $name);
            if (empty($response['id'])) {
                $this->error = "Не удалось добавить запись (pid: $id, name: $name)";
            } else {
                if ($this->isAjax) {
                    $this->set([
                        'id' => $response['id'],
                        'name' => $name
                    ]);
                }
            }
        }
    }

    public function addAction()
    {
        $this->layout = false;
        $model = new Main;

        $pid = $this->postParams['pid'] ?? $this->getParams['pid'] ?? null;
        $name = $this->postParams['name'] ?? $this->getParams['name'] ?? 'Root';

        if (isset($pid) && is_numeric($pid)) {
            $response = $model->addBranch($pid, $name);

            if (empty($response['id'])) {
                $this->error = "Не удалось добавить запись (pid: $pid, name: $name)";
            } else {
                $id = $response['id'];
                $pid = $response['pid'];
            }
        }

        if ($this->isAjax) {
            $this->set([
                'id' => $id,
                'pid' => $pid
            ]);
        }
    }

    public function deleteAction()
    {
        $this->layout = false;
        $model = new Main;

        $id = $this->postParams['id'] ?? $this->getParams['id'] ?? null;

        if (isset($id) && is_numeric($id)) {
            if (!$id = $model->deleteBranch($id)) {
                $this->error = "Не удалось удалить запись с id: $id";
            }
        }

        if ($this->isAjax) {
            $this->set([
                'id' => $id
            ]);
        }
    }

    private function buildTree($data, $parentId = 0)
    {
        $tree = array();

        foreach ($data as $element) {
            if ($element['pid'] == $parentId) {
                $children = $this->buildTree($data, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $tree[] = $element;
            }
        }

        return $tree;
    }

    private function buildTreeHtml($data, $parentId = 0)
    {
        $html = "";
        foreach ($data as $item) {
            if ($item['pid'] == $parentId) {
                $hasChildren = false;
                foreach ($data as $childItem) {
                    if ($childItem['pid'] == $item['id']) {
                        $hasChildren = true;
                        break;
                    }
                }

                $html .= "<li" . ($hasChildren ? ' class="treeview-animated-items"' : '') . ">";
                if ($hasChildren) {
                    $html .= '<div class="closed">';
                    $html .= '<i class="fas fa-angle-right"></i>';
                    $html .= '<span id="' . $item['id'] . '"><i class="far fa-folder ic-w me-2"></i>' . $item['name'] . '</span>';
                    $html .= '</div>';
                } else {
                    $html .= '<div id="' . $item['id'] . '" class="treeview-animated-element me-2"><i class="far fa-file-text ic-w me-2"></i>' . $item['name'] . '</div>';
                }
                $html .= '<div class="control_panel">';
                $html .= '<a href="/main/edit/?rename=' . $item['id'] . '" id="' . $item['id'] . '" class="rename_field_modal fa fa-edit" title="Переименовать"></a> ||';
                $html .= '<a href="/main/edit/?add=' . $item['id'] . '" id="' . $item['id'] . '" class="add_field_modal fa fa-add" title="Добавить"></a> ||';
                $html .= '<a href="/main/delete/?id=' . $item['id'] . '" id="' . $item['id'] . '" class=" ' . ($hasChildren ? 'delete_field_modal' : 'delete_field') . ' fa fa-remove" title="Удалить"></a>';
                $html .= '</div>';

                $children = $this->buildTreeHtml($data, $item['id']);
                if ($children) {
                    $html .= '<ul class="nested list-group-numbered parent-' . $item['id'] . '">';
                    $html .= $children;
                    $html .= '</ul>';
                }

                $html .= "</li>";
            }
        }
        return $html;
    }
}
