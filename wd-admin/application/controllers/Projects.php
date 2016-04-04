<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Projects extends MY_Controller {
    /*
     * Variável pública com o limite de projetos por página
     */

    public $limit = 10;

    public function __construct() {
        parent::__construct();
        $this->load->model('projects_model');
    }

    /*
     * Método para listar os projetos
     */

    public function index() {
        $search = $this->form_search();
        $projects = $search['projects'];
        $total_rows = $search['total_rows'];
        $pagination = $this->pagination($total_rows);

        $vars = [
            'title' => 'Projetos',
            'projects' => $projects,
            'pagination' => $pagination,
            'total' => $total_rows
        ];
        if ($this->data_user['dev_mode']) {
            // Template modo desenvolvedor
            $this->load->template('dev-projects/index', $vars);
        } else {
            // Template modo cliente
            $this->load->template('projects/index', $vars);
        }
    }

    /*
     * Método para busca e projetos
     */

    private function form_search() {
        $this->form_validation->set_rules('search', 'Pesquisa', 'trim|required');
        $this->form_validation->run();
        $dev_mode = $this->data_user['dev_mode'];
        $limit = $this->limit;
        $keyword = $this->input->get('search');
        $perPage = $this->input->get('per_page');
        $projects = $this->projects_model->search($dev_mode, $keyword, $limit, $perPage);
        $total_rows = $this->projects_model->search_total_rows($dev_mode, $keyword);
        return array(
            'projects' => $projects,
            'total_rows' => $total_rows
        );
    }

    /*
     * Método de criação de template da páginação da listagem de projetos
     */

    private function pagination($total_rows) {
        $this->load->library('pagination');
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $this->limit;
        $config['page_query_string'] = true;
        $config['reuse_query_string'] = true;
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_open'] = '</li>';
        $config['first_url'] = '?per_page=0';

        $this->pagination->initialize($config);
        return $this->pagination->create_links();
    }

    public function create() {
        func_only_dev();
        $this->form_create();

        add_js([
            'view/projects/js/form.js'
        ]);
        $vars = [
            'title' => 'Novo projeto',
            'name' => '',
            'directory' => '',
            'database' => '',
            'main' => '',
            'status' => '',
            'suffix' => ''
        ];
        $this->load->template('dev-projects/form', $vars);
    }

    /*
     * Método para criação de projeto
     */

    private function form_create() {
        $this->form_validation->set_rules('name', 'Nome', 'trim|required');
        $this->form_validation->set_rules('suffix', 'Sufixo', 'trim|required|max_length[6]');
        $this->form_validation->set_rules('dir', 'Diretório', 'trim|required|callback_verify_dir');
        if ($this->form_validation->run()) {
            $name = $this->input->post('name');
            $slug = $this->slug($name);
            $dir = slug($this->input->post('dir'));
            $main = $this->input->post('main');
            $suffix = str_replace('_', '', $this->input->post('suffix')) . '_';
            $status = $this->input->post('status');
            $create_db = false;

            $user = $this->data_user;
            $data = [
                'name' => $name,
                'dir' => $dir,
                'slug' => $slug,
                'id_user' => $user['id'],
                'suffix' => $suffix,
                'main' => $main,
                'status' => $status
            ];
            $create = $this->projects_model->create($data);
            if ($create) {
                // Se o projeto for criado com sucesso, é extraido um projeto em codeigniter na pasta inicial
                $this->extractProject($data);
            }
            redirect('projects');
        } else {
            setError('errors_create', validation_errors());
        }
    }

    /*
     * Método para editar projeto
     */

    public function edit($slug_project) {
        func_only_dev();
        $project = $this->projects_model->get_project($slug_project);
        if (!$project) {
            redirect('projects');
        }
        $this->form_edit($project);
        add_js([
            'view/projects/js/form.js'
        ]);
        $suffix = $project['suffix'];
        $vars = [
            'title' => 'Editar projeto',
            'name' => $project['name'],
            'directory' => $project['directory'],
            'suffix' => $suffix,
            'status' => $project['status'],
            'main' => $project['main']
        ];
        $this->load->template('dev-projects/form', $vars);
    }

    /*
     * Método de configuração dos requisitos para edição de projeto
     */

    private function form_edit($project) {
        $this->form_validation->set_rules('name', 'Nome', 'trim|required');
        if ($this->form_validation->run()) {
            $name = $this->input->post('name');
            $slug = $this->slug($name, $project['id']);
            $status = $this->input->post('status');
            $data = [
                'name' => $name,
                'slug' => $slug,
                'status' => $status,
                'project' => $project['id']
            ];
            $this->projects_model->edit($data);
            redirect('projects');
        } else {
            setError('errors_edit', validation_errors());
        }
    }

    /*
     * Método para verificar existencia de diretório
     */

    public function verify_dir($dir) {
        $main = $this->input->post('main');
        if ($main) {
            $mainExists = $this->projects_model->main_exists();
            if ($mainExists && is_dir('../' . $mainExists['directory'])) {
                $this->form_validation->set_message('verify_dir', 'Já existe um diretório principal.');
                return false;
            }
        }
        if (is_dir('../' . $dir) or is_dir(getcwd() . '/application/views/project/' . $dir)) {
            // Se o diretório já existir no admin ou no diretório inicial
            $this->form_validation->set_message('verify_dir', 'Esse diretório já existe.');
            return false;
        } elseif (!$this->createDir($dir, $main)) {
            // Se não for possível criar o diretório do projeto
            $this->form_validation->set_message('verify_dir', 'Não foi possível criar o diretório.');
            return false;
        } else {
            return true;
        }
    }

    /*
     * Método para criar diretórios
     */

    protected function createDir($dir, $main) {
        $dir_project = '../' . $dir;
        $dir_admin = getcwd() . '/application/views/project/' . $dir;

        if (!@mkdir($dir_admin, 0755)) {
            return false;
        } elseif (!@mkdir($dir_project, 0755)) {
            \unlink($dir_admin);
            return false;
        }

        return true;
    }

    /*
     * Método de extração de projeto codeigniter padrão
     */

    protected function extractProject($data) {
        $dir = $data['dir'];
        $main = $data['main'];
        $dir_project = '../' . $dir;

        $file = getcwd() . '/application/files_project/project_default.zip';
        $to = $dir_project;

        $zip = new ZipArchive;
        $zip->open($file);
        if ($zip->extractTo($to)) {
            // Se extraido com sucesso, faz as configurações necessárias para o novo projeto
            $this->configProject($data);
        }
        $zip->close();
    }

    /*
     * Método para configuração do projeto extraido
     */

    protected function configProject($data) {
        $dir_project = $data['dir'];
        $main = $data['main'];

        $dir_system = '../' . DIR_ADMIN_DEFAULT . 'system';
        $dir_application = 'application';

        if ($main) {
            // As configurações mudam, caso seja o projeto principal
            $dir_system = DIR_ADMIN_DEFAULT . 'system';
            $dir_application = $dir_project;
        }

        // Config index.php
        $path_index = '../' . $dir_project . '/index.php';
        $index = file_get_contents($path_index);
        $index = str_replace([
            '[[system_path]]',
            '[[application_folder]]'
                ], [
            $dir_system,
            $dir_application
                ], $index);
        file_put_contents($path_index, $index);

        // Config database 
        $path_config_db = '../' . $dir_project . '/application/config/database.php';

        $data_db = $this->db;
        $db = $data['database'];
        $hostname_db = $data_db->hostname;
        $username_db = $data_db->username;
        $pass_db = $data_db->password;

        $config_db = file_get_contents($path_config_db);
        $config_db = str_replace([
            '[[hostname]]',
            '[[username]]',
            '[[password]]',
            '[[database]]',
                ], [
            $hostname_db,
            $username_db,
            $pass_db,
            $db
                ], $config_db);
        file_put_contents($path_config_db, $config_db);


        if ($main) {
            rename($path_index, '../index.php');

            $dir_application_from = '../' . $dir_application . '/application';
            $dir_application_to = '../' . $dir_application . '/';
            $list_dir = dir($dir_application_from);
            while ($file = $list_dir->read()) {
                rename($dir_application_from . $file, $dir_application_to . $file);
            }
            rmdir($dir_application_from);
        }
    }

    /*
     * Método para verificar a existencia do slug no banco de dados
     */

    protected function slug($name, $id = false) {
        $return = true;
        $slug = null;
        $i = 0;
        while ($return == true) {
            $slug = slug($name);
            if ($i > 0) {
                $slug .= $i;
            }
            $exe = $this->projects_model->verify_slug($slug, $id);
            ++$i;
            $return = ($exe);
        }
        return $slug;
    }

    /*
     * Método para remover projeto
     */

    public function delete($slug_project) {
        func_only_dev();
        $project = $this->projects_model->get_project($slug_project);
        if (!$project) {
            redirect('projects');
        }
        $this->form_delete($project);
        $vars = [
            'title' => 'Remover projeto',
            'project' => $project
        ];
        $this->load->template('dev-projects/delete', $vars);
    }

    /*
     * Método com configuração dos requisitos para remover projeto
     */

    private function form_delete($project) {
        $this->form_validation->set_rules('project', 'Projeto', 'trim|required|integer');
        if ($this->form_validation->run()) {
            if ($project['id'] == $this->input->post('project')) {
                $delete_all = $this->input->post('delete_all');
                $this->projects_model->delete($project['id']);
                $dir_project = $project['directory'];
                // Remove todos controllers
                forceRemoveDir(getcwd() . '/application/controllers/Project/' . $dir_project);
                // Remove todas models
                forceRemoveDir(getcwd() . '/application/models/Project/' . $dir_project);
                // Remove todos arquivos de assets
                forceRemoveDir(getcwd() . '/assets/project/' . $dir_project);
                // Remove todos arquivos de views
                forceRemoveDir(getcwd() . '/application/views/project/' . $dir_project);
                if ($delete_all) {
                    // Remove todos os arquivos do projeto no diretório inicial
                    forceRemoveDir('../' . $dir_project);
                    redirect('projects');
                }
            }
        }
    }

}