<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
?>
<div class="header">
    <h1 class="page-title"><?php echo $title ?></h1>
</div>

<ul class="breadcrumb">
    <li><a href="<?php echo base_url() ?>">Home</a></li>
    <li><a href="<?php echo base_url_app() ?>">Projetos</a></li>
    <li class="active"><?php echo $title ?></li>
</ul>

<div class="container-fluid">
    <?php echo form_open(); ?>
    <div class="btn-toolbar">

    </div>
    <?php
    echo getErrors();
    echo form_open(null, ['class' => 'form-horizontal']);
    ?>
    <div class="tab-pane active in" id="home">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nome*</label>
                    <input type="text" name="name" id="dig_name" value="<?php echo set_value('name', $name) ?>" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Diretório*</label>
                    <input type="text" name="dir" id="dir" value="<?php echo set_value('dir', $directory) ?>" class="form-control" <?php if ($this->uri->segment('3') == 'edit') {echo 'disabled';}?>>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Sufixo para criação de tabelas*</label>
                    <input type="text" name="suffix" id="suffix" maxlength="6" value="<?php echo set_value('suffix', $suffix) ?>" class="form-control" <?php if ($this->uri->segment('3') == 'edit') {echo 'disabled';}?>>
                </div>
                <?php if($main == 1){?><strong>Obs: Projeto principal não possui sufixo.</strong><?php }?>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Status*</label>
                    <select name="status" class="form-control">
                        <option value="1" <?php echo set_select('status', '1', ($status == '1')) ?>>Ativado</option>
                        <option value="0" <?php echo set_select('status', '0', ($status == '0')) ?>>Desativado</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group text-right">
            <input class="btn btn-primary" value="Salvar" name="send" type="submit">
        </div>
    </div>
<?php echo form_close(); ?>
</div>