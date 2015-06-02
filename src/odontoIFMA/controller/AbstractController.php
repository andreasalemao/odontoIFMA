<?php

namespace odontoIFMA\controller;


abstract class AbstractController
{
    protected $app;
    protected $em;
    protected $entity;

    public function insert($dados)
    {
        if (null != $dados) {
            $entity = new $this->entity($dados);
            $this->persist($entity);
            return $entity;
        }

        return null;
    }

    public function update(array $dados)
    {
        if (null != $dados && isset($dados['id'])) {
            $entity = $this->em->getRepository($this->entity)->find($dados['id']);

            if ($entity) {
                $entity->__construct($dados);
                $this->persist($entity);
                return $entity;
            }
        }
        return null;
    }

    /**
     * @param Integer $id
     * @return Integer / null
     * @throws \Exception
     */
    public function delete($id)
    {
        if (null != $id) {
            $entity = $this->em->getRepository($this->entity)->find($id);

            if ($entity) {
                $this->em->remove($entity);
                $this->em->flush();
                return $id;
            } else {
                throw new \Exception('Registro não encontrado.');
            }
        }
        return null;
    }

    protected function persist($entity)
    {
        $this->em->getConnection()->beginTransaction();
        try {
            $this->em->persist($entity);
            $this->em->flush();
            $this->em->getConnection()->commit();
        } catch (\Exception $exc) {
            $this->em->getConnection()->rollback();
            $this->em->close();
            throw $exc;
        }

        return $entity;
    }

}