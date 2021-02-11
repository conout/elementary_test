<?php

namespace App\Controller;

use App\Entity\Country;
use App\Form\CountryType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CountryController extends AbstractController
{
    /**
     * @Route("/country", name="country")
     */
    public function index(): Response
    {
        try {
            $country = $this->getDoctrine()->getRepository(Country::class)
                ->findAll();
        } catch(Exception $e) {
            $this->addFlash("db_error", "Не удалось получить данные.");
            return $this->render('error.html.twig', ['title' => 'Ошибка']);
        }

        return $this->render('country/index.html.twig', [
            'title' => 'Страны',
            'country' => $country,
        ]);
    }

    /**
     * @Route("/country/create", name="country_create")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function create(Request $request)
    {
        $new_country = new Country();
        $form = $this->createForm(CountryType::class, $new_country);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();

            try {
                $em->persist($new_country);
                $em->flush();
                $em->getConnection()->commit();
            } catch (Exception $e) {
                $em->getConnection()->rollback();
                throw $e;
            }

            return $this->redirectToRoute('country');
        }

        return $this->render('country/form.html.twig', [
            'title' => 'Добавление страны',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/country/update/{id}", name="country_update")
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function update(int $id, Request $request)
    {
        $country = $this->getDoctrine()->getRepository(Country::class)
            ->find($id);
        $form = $this->createForm(CountryType::class, $country);
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

            return $this->redirectToRoute('country');
        }

        return $this->render('country/form.html.twig', [
            'title' => 'Редактирование страны',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/country/delete/{id}", name="country_delete")
     * @param int $id
     * @return RedirectResponse
     * @throws Exception
     */
    public function delete(int $id): RedirectResponse
    {
        $country = $this->getDoctrine()->getRepository(Country::class)
            ->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();

        try {
            $em->remove($country);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }

        return $this->redirectToRoute('country');
    }
}