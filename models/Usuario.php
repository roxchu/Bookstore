<?php 
class Usuario{
    //atributos
    private int $id;
    private string $realname;
    private string $username;
    private string $pass;
    private string $email;
    private string $telefono;
    private string $direccion;
    private ?int $idRol;

    //constructor
    public function __construct(
        int $id,
        string $realname,
        string $username,
        string $pass,
        string $email,
        string $telefono,
        string $direccion,
        int $idRol,
    ){
        $this -> id = $id;
        $this -> realname = $realname;
        $this ->username = $username;
        $this -> pass = $pass;
        $this -> email = $email;
        $this -> telefono = $telefono;
        $this -> direccion = $direccion;
        $this -> idRol =  $idRol;
    }

    //getters 
    public function getID(): int{
        return $this-> id;
    }

    public function getRealname(): string{
        return $this ->realname;
    }

    public function getUserName(): string{
        return $this-> username;
    }

    public function getPass(): string{
        return $this -> pass;
    }

    public function getEmail(): string{
        return $this->email;
    }

    public function getTelefono(): string{
        return $this -> telefono;
    }

    public function getDireccion(): string{
        return $this->direccion;
    }

    public function getIdRol(): int{
        return $this->idRol;
    }

    //setters
    public function setId(int $id): void{
     $this -> id = $id;
    }

    public function setRealname(string $realname): void{
     $this -> realname = $realname;
    }

    public function setUserName(string $username): void{
     $this -> username = $username;
    }

    public function setPass(string $pass): void{
     $this -> pass = $pass;
    }

    public function setEmail(string $email): void{
     $this -> email = $email;
    }

    public function setTelefono(string $telefono): void{
     $this -> telefono = $telefono;
    }

    public function setDireccion(string $direccion): void{
     $this -> direccion = $direccion;
    }

    public function setIdRol(int $idRol): void{
     $this -> idRol = $idRol;
    }
}
?>