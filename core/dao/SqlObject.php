<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace core\dao;

/**
 * Description of SqlObject
 *
 * @author jlgre_000
 */
class SqlObject {

    //put your code here
    private $connection;

    /**
     * Construtor
     * @param \PDO $connection Um objeto PDO para fazer a conexão com o banco de dados.
     */
    public function __construct(\PDO $connection) {
        $this->connection = $connection;
    }

    /**
     * 
     * @param array $data Um array associativo no formato chave=>valor onde chave é o nome do campo da tabela do banco de dados e valor o dado.
     */
    private function transform(&$data) {
        foreach ($data as $key => $value) {
            if (is_string($value))
                $data[$key] = "'{$data[$key]}'";
            else if (is_null($value) || is_string($value) && $value == "")
                $data[$key] = 'null';
            else if (is_bool($value))
                $data[$key] == true ? $data[$key] = 'TRUE' : $data[$key] = 'FALSE';
        }
    }

    /**
     * Insere dados em uma tabela do banco de dados.
     * @param string $table O nome da tabela do banco de dados.
     * @param array $data Um array no formato chave=>valor onde chave é o nome do campo do banco de dados e valor é o dado a ser inserido.
     * @param boolean $seqId Retorna o id inserido 
     * @return int o id que foi inserido
     * @throws \Exception
     */
    public function insert($table, $data, $seqId = null) {
        $this->transform($data);
        $sql = "insert into $table (";
        $sql .= implode(', ', array_keys($data)) . ')';
        $sql .= ' values (';
        $sql .= implode(', ', array_values($data)) . ');';                       
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            if ($seqId) {
                return $this->connection->lastInsertId($seqId);
            }
        } catch (\PDOException $ex) {
            throw new \Exception('Erro de Inserção no BD: ' . $ex->getMessage() .
            '. SQL: ' . $sql, null, $ex);
        } finally {
            $this->connection = null;           
        }
    }

    
    /**
     * Atualiza um registro na tabela de dados.
     * @param string $table A tabela a ser atualizada.
     * @param array $data Um array no formato chave=>valor onde chave é o nome do campo do banco de dados e valor é o dado a ser inserido.
     * @param String $criteria O critério de atualização. Exemplo: 'id = $pid'.
     * @throws \Exception
     */
    public function update($table, $data, $criteria) {
        $this->transform($data);
        $sql = "update $table set ";
        $set = '';
        foreach ($data as $key => $value) {
            $set[] = $key . ' = ' . $value;
        }
        $sql .= implode(', ', $set);
        $sql .= " where $criteria ;";                        
        //echo "<strong>SQL Update:</strong> $sql<br>";
        try {
            $this->connection->exec($sql);
        } catch (\PDOException $ex) {
            throw new \Exception('Erro de atualização no BD: ' .
            $ex->getMessage() . '. SQL: ' . $sql, null, $ex);
        } finally {
            $this->connection = null;               
        }
    }

    /**
     * Exclui um registro no banco de dados.
     * @param string $table A tabela que terá o seu registro excluído.
     * @param string $criteria O critério de exclusão. Exemplo: 'id = $id';
     * @throws \Exception
     */
    public function delete($table, $criteria) {
        try {
            $sql = "delete from {$table} where {$criteria};";
            $this->connection->exec($sql);
        } catch (\PDOException $ex) {
            throw new \Exception('Erro de exclusão no BD: ' .
            $ex->getMessage() . '. SQL: ' . $sql, null, $ex);
        } finally {
            $this->connection = null;
        }
    }
    
    /**
     * Executa um select no banco de dados.
     * @param string $table O nome da tabela do banco de dados.
     * @param string $columns O nomes dos campos da tabela separados por vírgula.
     * @param string $criteria O critério ou filtro do select. Exemplo: 'id = $id';
     * @param string $orderBy O campo ou campos que serão usados para ordenar os dados.
     * @param string $groupBy O campo ou campos que serão usados para agrupar os dados.
     * @param int $limit O limite de dados que serão retornados.
     * @return array
     * @throws \PDOException
     */
    public function select($table, $columns, $criteria = null, 
            $orderBy = null, $groupBy = null, $limit = null, $offset = null) {
        $sql = "select $columns from $table ";
        if (!is_null($criteria)) {
            $sql .= " where $criteria ";
        }
        if (!is_null($orderBy)) {
            $sql .= " order by $orderBy";
        }
        if (!is_null($groupBy)) {
            $sql .= " group by $groupBy";
        }
        if (!is_null($limit)) {
            $sql .= " limit $limit";
        }   
        if(!is_null($offset)){
            $sql .= " offset $offset";
        }
        try {            
            $result = $this->connection->query($sql, \PDO::FETCH_ASSOC);
            $data = null;
            foreach ($result as $reg) {
                $data[] = $reg;
            }            
            return $data;
        } catch (\PDOException $ex) {
            throw new \PDOException('Erro de seleção no BD: ' .
            $ex->getMessage() . ". Instrução SQL: " . $sql, null, $ex);
        } finally {
            $this->connection = null;  
            //echo "<br>SQL Select: $sql";
        }
    }

}
