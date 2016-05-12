<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
?>
<!-- page content -->
<div class="right_col" role="main">
    <ul class="breadcrumb">
        <li><a href="<?php echo base_url(); ?>">Home</a></li>
        <li class="active"><?php echo $title ?></li>
    </ul>

    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2><?php echo $title ?></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">

                    <?php echo form_open(); ?>
                    <div class="btn-toolbar">

                    </div>
                    <?php
                    echo validation_errors('<p class="alert alert-danger">', '</p>');
                    echo form_open(null, ['class' => 'form-horizontal']);
                    ?>
                    <div class="tab-pane active in" id="home">
                        <div class="row">
                            <div class="col-sm-2">
                                <a href="#gallery" class=" btn-upload" data-toggle="modal">
                                    <span class="fa fa-edit icon-edit">Alterar</span>
                                    <img src="<?php
                                    if (is_file('../wd-content/upload/'.$image)) {
                                        echo wd_base_url('wd-content/upload/' . $image);
                                    } else {
                                        echo base_url('assets/images/user.png');
                                    }
                                    ?>" alt="Avatar" class="img-circle profile_img" id="img-profile" height="109">
                                </a>
                                <input type="hidden" name="image" value="<?php echo set_value('image', $image);?>" id="upload-image">
                            </div>
                            <div class="col-sm-10">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nome*</label>
                                            <input type="text" name="name" value="<?php echo set_value('name', $name) ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Sobrenome</label>
                                            <input type="text" name="lastname" value="<?php echo set_value('lastname', $last_name) ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Email*</label>
                                            <input type="email" name="email" value="<?php echo set_value('email', $email) ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Login*</label>
                                            <input type="text" name="login" value="<?php echo set_value('login', $login) ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Senha*</label>
                                            <div class="input-group">
                                                <input type="password" name="password" value="<?php echo set_value('password') ?>" class="form-control input-pass"> 
                                                <a href="#gerar-senha" class="btn btn-default generate-pass input-group-addon" data-toggle="modal">Gerar senha</a>
                                            </div>
                                            <small>* Preencha somente para alterar</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Sobre</label>
                                            <textarea name="about" class="form-control"><?php echo set_value('about',$about)?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <input class="btn btn-primary" value="Salvar" name="send" type="submit">
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                    <div class="modal fade" id="gerar-senha" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Senha gerada</h4>
                                </div>
                                <div class="modal-body">
                                    <h2 class="get-password"></h2>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
                                    <button class="btn btn-primary bt-ok" data-dismiss="modal">Copiado</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="gallery" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Envie ou selecione arquivos</h4>
                </div>
                <div class="modal-body">
                    <?php echo form_open_multipart('apps/gallery/upload', ['id' => 'dropzone_gallery', 'class' => 'dropzone form-group']) ?>
                    <div class="dropzone-previews"></div>
                    <div class="dz-default dz-message"></div>
                    <?php echo form_close() ?>
                    <?php echo form_open(null, ['method' => 'get', 'class' => 'form-group', 'id' => 'search-files']); ?>
                    <div class="input-group">
                        <input type="text" name="search" id="search-field" value="<?php echo $this->input->get('search') ?>" placeholder="Procurar arquivo" class="input-sm form-control"> 
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-primary"> Buscar</button> 
                        </span>
                    </div>
                    <?php echo form_close(); ?>
                    <div class="row" id="files-content">
                        <!--EJS -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="btn-save-change">Salvar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <!--EJS-->
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <!--EJS-->
            </div>
        </div>
    </div>
</div>