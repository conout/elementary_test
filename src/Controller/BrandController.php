<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Country;
use App\Form\BrandType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BrandController extends AbstractController
{
    /**
     * @Route("/brand", name="brand")
     */
    public function index()
    {
        try {
            $brand = $this->getDoctrine()->getRepository(Brand::class)
                ->findAll();
        } catch(Exception $e) {
            $this->addFlash("db_error", "Не удалось получить данные.");
            return $this->render('error.html.twig', ['title' => 'Ошибка']);
        }

        return $this->render('brand/index.html.twig', [
            'title' => 'Марки',
            'brand' => $brand,
        ]);
    }

    /**
     * @Route("/brand/create", name="brand_create")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function create(Request $request)
    {
        $new_brand = new Brand();
        $form = $this->createForm(BrandType::class, $new_brand);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();

            try {
                $em->persist($new_brand);
                $em->flush();
                $em->getConnection()->commit();
            } catch (Exception $e) {
                $em->getConnection()->rollback();
                throw $e;
            }

            return $this->redirectToRoute('brand');
        }

        return $this->render('brand/form.html.twig', [
            'title' => 'Добавление марки',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/brand/update/{id}", name="brand_update")
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function update(int $id, Request $request)
    {
        $brand = $this->getDoctrine()->getRepository(Brand::class)
            ->find($id);
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();

            try {
                $em->flush();
                $em->getConnection()->commit();
            } catch (Exception $e) {
                $em->getConnection()->rollback();
                throw $e;
            }

            return $this->redirectToRoute('brand');
        }

        return $this->render('brand/form.html.twig', [
            'title' => 'Редактирование марки',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/brand/delete/{id}", name="brand_delete")
     * @param int $id
     * @return RedirectResponse
     * @throws Exception
     */
    public function delete(int $id): RedirectResponse
    {
        $brand = $this->getDoctrine()->getRepository(Brand::class)
            ->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();

        try {
            $em->remove($brand);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }

        return $this->redirectToRoute('brand');
    }
}