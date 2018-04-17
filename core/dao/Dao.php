<?php
namespace core\dao;

/**
 * Classe que abstrai métodos de acesso ao banco de dados.
 *
 * @author jlgre_000
 */
abstract class Dao
{

    /**
     * O modelo que será manipulado pelo Dao.
     * @var core\mvc\Model 
     */
    protected $model;

    /**
     * O objeto PDO para fazer a conexão com o BD
     * @var \PDO
     */
    protected $connection;

    /**
     * Um array contendo o nome das colunas da tabela
     * @var array 
     */
    protected $columns;

    /**
     * O Nome da Tabela
     * @var string 
     */
    protected $tableName;

    /**
     * O Nome do campo que armazena o ID na tabela.
     * @var string 
     */
    protected $tableId;


    /**
     * Atribui o objeto Model
     * @param \core\mvc\Model $model O modelo que o DAO irá manipular.
     */
    public function setModel(\core\mvc\Model $model)
    {
        $this->model = $model;
        $this->setColumns();
    }

    /**
     * Retorna o modelo associado ao Dao
     * @return \core\mvc\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Retorna um array contendo o os nomes dos campos da tabela.
     * @return array 
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Retorna o nome do campo que define o id
     * @return string 
     */
    public function getTableId()
    {
        return $this->tableId;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Este método deve criar um array cujo as chaves devem ser os nomes da tabela e os valores devem vir do respectivo model.
    */
    protected abstract function setColumns();

    /**
     * Persiste o modelo no banco de dados.
     * @throws \Exception
     */
    public function insertUpdate($returnId = null)
    {
        try {
            //..abre a conexão com o banco de dados
            $this->connection = Connection::getConnection();
            //..cria um novo objeto SQLObject
            $sqlObj = new SqlObject($this->connection);
            //..se tiver id, então é inserção
            if (!$this->model->getId()){
                if ($returnId) //..retorna o id inserido
                    return $sqlObj->insert($this->tableName, $this->columns, $returnId);
                else //..insere sem retonar o id
                    $sqlObj->insert($this->tableName, $this->columns);
            }
            else{ //..senão, é atualização
                $sqlObj->update($this->tableName, $this->columns, $this->tableId . " = {$this->model->getId()}");
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Exclui um objeto do banco de dados.
     * @param int $id O id do objeto a ser excluído.
     * @throws \Exception
     */
    public function delete($id)
    {
        try {
            //..abre a conexão como banco de dados
            $this->connection = Connection::getConnection();
            //..cria um novo objeto SQLObject
            $sqlObj = new SqlObject($this->connection);
            //..exclui um registro
            $sqlObj->delete($this->tableName, $this->tableId . " = $id");
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Método para localizar um objeto no Banco de dados
     * @param int $id O id do objeto a ser excluído.
     */
    public function findById($id)
    {
        $this->connection = Connection::getConnection();
        $sqlObj = new SqlObject($this->connection);
        $data = $sqlObj->select(
            $this->tableName,'*',
            "{$this->tableId} = $id "
        );
        $data = $data[0]; //..seta para o primeiro registro;
        return $data;
    }

    /**
     * Método para listar objetos do banco de dados.
     * @param string $criteria O critério para exclusão. Ex.: "'id' = $id";
     * @param string $orderBy Campo ou campos separados por vírgula que serão usados como critério de ordenação.
     * @param string $groupBy Campo ou campos separados por vírgula que serão usados como critério de agrupamento.
     * @param int $limit Define um limite para a qtde de dados que serão retornados pela consulta SQL.
     */
    public function selectAll($criteria = null, $orderBy = null, 
        $groupBy = null, $limit = null, $offSet = null)
    {
        $this->connection = Connection::getConnection();
        $sqlObj = new SqlObject($this->connection);
        $data = $sqlObj->select($this->tableName, '*', $criteria, $orderBy, $groupBy, $limit, $offSet);
        return $data;
    }

    //..método para contar quantos registros uma query retorna
    public function selectCount($criteria)
    {
        try {
            //..cria o objeto para interagir com o BD
            $sqlObj = new \core\dao\SqlObject(\core\dao\Connection::getConnection());
            $dados = $sqlObj->select($this->tableName, 'count(*)', $criteria);
            return (int)$dados[0]['count'];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}
