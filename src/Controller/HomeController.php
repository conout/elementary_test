<?php

namespace App\Controller;

use App\Entity\Car;
use App\Form\CarType;
use App\Form\FilterFormType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);

        $car = $this->getDoctrine()->getRepository(Car::class)
            ->createQueryBuilder('c');

        // применение фильтра
        if ($form->get('search')->isClicked() && $form->isSubmitted() && $form->isValid())
        {
            $filter_params = $request->request->get('filter_form');

            if(!empty($filter_params['year_from']))
                $car = $car->andWhere('c.year >= :year_from')
                    ->setParameter('year_from', $filter_params['year_from']);

            if(!empty($filter_params['year_to']))
                $car = $car->andWhere('c.year <= :year_to')
                    ->setParameter('year_to', $filter_params['year_to']);

            if(!empty($filter_params['price_from']))
                $car = $car->andWhere('c.price >= :price_from')
                    ->setParameter('price_from', $filter_params['price_from']);

            if(!empty($filter_params['price_to']))
                $car = $car->andWhere('c.price <= :price_to')
                    ->setParameter('price_to', $filter_params['price_to']);

            if(!empty($filter_params['brand']))
                $car = $car->andWhere('c.brand = :brand')
                    ->setParameter('brand', $filter_params['brand']);

            if(!empty($filter_params['country']))
                $car = $car->andWhere('c.country = :country')
                    ->setParameter('country', $filter_params['country']);

        }

        try {
            $car = $car->getQuery()->execute();
        } catch(Exception $e) {
            $this->addFlash("db_error", "Не удалось получить данные.");
            return $this->render('error.html.twig', ['title' => 'Ошибка']);
        }

        return $this->render('home/index.html.twig', [
            'title' => 'Объявления',
            'car' => $car,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/view/{id}", name="view")
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function view(int $id)
    {
        try {
            $car = $this->getDoctrine()->getRepository(Car::class)
                ->find($id);
        } catch(Exception $e) {
            $this->addFlash("db_error", "Не удалось получить данные.");
            return $this->render('error.html.twig', ['title' => 'Ошибка']);
        }

        return $this->render('home/view.html.twig', [
            'car' => $car,
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function create(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $new_car = new Car();
        $form = $this->createForm(CarType::class, $new_car);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $new_car->setCreateAtValue();
            $new_car->setEditAtValue();

            $em->getConnection()->beginTransaction();

            try {
                $em->persist($new_car);
                $em->flush();
                $em->getConnection()->commit();
            } catch (Exception $e) {
                $em->getConnection()->rollback();
                throw $e;
            }

            return $this->redirectToRoute('view', ['id' => $new_car->getId()]);
        }

        return $this->render('home/form.html.twig', [
            'title' => 'Добавление объявления',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/update/{id}", name="update")
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function update(int $id, Request $request)
    {
        $car = $this->getDoctrine()->getRepository(Car::class)->find($id);
        $form = $this->createForm(CarType::class, $car);
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

            $em->flush();
            return $this->redirectToRoute('view', ['id' => $car->getId()]);
        }

        return $this->render('home/form.html.twig', [
            'title' => 'Редактирование объявления',
            'form' => $form->createView(),
            'car' => $car,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @param int $id
     * @return RedirectResponse
     * @throws Exception
     */
    public function delete(int $id): RedirectResponse
    {
        $car = $this->getDoctrine()->getRepository(Car::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();

        try {
            $em->remove($car);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }

        return $this->redirectToRoute('home');
    }
}