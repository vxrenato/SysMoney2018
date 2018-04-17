<?php
namespace core\dao;

/**
 * Description of Connection
 *
 * @author jlgre_000
 */
class Connection {

    /**
     * O nome do banco de dados
     */
    const DBNAME = 'sysmoney';
    /**
     * O usuário do banco de dados
     */
    const USER = 'postgres';
    /**
     * A senha do banco de dados
     */
    const PASSWORD = 'postdba';
    /**
     * O host do banco de dados. Por padrão é 127.0.0.1 ou localhost
     */
    const HOST = 'localhost';
    /**
     * A porta do banco de dados. O PosgreSQL usa por padrão a porta 5432.
     */
    const PORT = 5432;

    /**
     * Retorna um objeto PDO para fazer a conexão com o banco de dados.
     * @return \PDO
     * @throws \PDOException
     */
    public static function getConnection() {
        try {
            //..pega um objeto PDO
            $connection = new \PDO("pgsql:dbname=" . self::DBNAME .
                    ";user=" . self::USER .
                    ";password=" . self::PASSWORD . ";host=" .
                    self::HOST . ";port=" . self::PORT);
            //..configura para gerar exceções sempre que um erro ocorrer
            $connection->setAttribute(\PDO::ATTR_ERRMODE, 
                    \PDO::ERRMODE_EXCEPTION);
            //..retorna o objeto PDO;
            return $connection;
        } catch (\PDOException $ex) {
            throw $ex;
        }
    }
}