<?php

namespace App\Dto;

class TransacaoDto{

    private ?int $id = null;
    private ?string $dataHora = null; 
    private ?string $valor = null;    
    private ?int $contaOrigemId = null; 
    private ?int $contaDestinoId = null;

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of dataHora
     */ 
    public function getDataHora()
    {
        return $this->dataHora;
    }

    /**
     * Set the value of dataHora
     *
     * @return  self
     */ 
    public function setDataHora($dataHora)
    {
        $this->dataHora = $dataHora;

        return $this;
    }

    /**
     * Get the value of valor
     */ 
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set the value of valor
     *
     * @return  self
     */ 
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get the value of contaOrigemId
     */ 
    public function getContaOrigemId()
    {
        return $this->contaOrigemId;
    }

    /**
     * Set the value of contaOrigemId
     *
     * @return  self
     */ 
    public function setContaOrigemId($contaOrigemId)
    {
        $this->contaOrigemId = $contaOrigemId;

        return $this;
    }

    /**
     * Get the value of contaDestinoId
     */ 
    public function getContaDestinoId()
    {
        return $this->contaDestinoId;
    }

    /**
     * Set the value of contaDestinoId
     *
     * @return  self
     */ 
    public function setContaDestinoId($contaDestinoId)
    {
        $this->contaDestinoId = $contaDestinoId;

        return $this;
    }
}



?>