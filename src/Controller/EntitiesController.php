<?php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Note;
use App\Repository\CategoryRepository;
use App\Repository\NoteRepository;
use App\Services\GenerateContent;
use App\Services\Utilities;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\String\u;

class EntitiesController extends AbstractController
{

    #[Route("/categories/create", name: "app_create_categories")]
    function createCategories(CategoryRepository $categoryRepository, GenerateContent $generateContent)
    {
        foreach ($generateContent->getCategories() as $category) {
            $new = new Category();
            $new->setDescription($category);
            $categoryRepository->add($new, true);
        }


        return new Response("Categories added");
    }


    #[Route("/notes/create", name: "app_create_notes")]
    function createNotes(NoteRepository $noteRepository, GenerateContent $generateContent, CategoryRepository $categoryRepository)
    {
        foreach ($generateContent->getNotes() as $notes) {
            $new = new Note();
            $new->setDescription($notes["description"]);
            $new->setIdCategory($categoryRepository->find($notes["category"]));
            $noteRepository->add($new, true);
        }

        return new Response("Notes added");

    }


    #[Route("/", name: "app_list")]
    function noteList(NoteRepository $noteRepository)
    {
        $notes = $noteRepository->findAll();

        return $this->render("homepage.html.twig", ["notes" => $notes]);
    }

    #[Route("/note/{slug}", name: "app_detail")]
    function noteDetail(string $slug, NoteRepository $noteRepository, Utilities $utilities)
    {

        $note = $noteRepository->find($slug);
        return $this->render("note.html.twig", ["note" => $note, "img" => $utilities->getFile(), "prettyDate" => $utilities->formatDate($note->getCreatedAt())]);
    }

    #[Route("/notes/add/", name: "app_add")]
    function noteAdd(Request $request, NoteRepository $noteRepository, CategoryRepository $categories)
    {
        $note = new Note();
        $categories = $categories->findAll();
        $form = $this->createFormBuilder($note)->add("description")->add("idCategory", ChoiceType::class, [
            "choices" => [
                "Category 1" => $categories[0],
                "Category 2" => $categories[1]
            ]
        ])->add("createdAt")->add("addBtn", SubmitType::class, ["label" => "Add", "attr" => ["class" => "btn btn-primary"]])->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new = $form->getData();
            $noteRepository->add($new, true);
            $this->addFlash("success", "Post added");
            return $this->redirectToRoute("app_list");

        }

        return $this->render("form.html.twig", ["form" => $form]);

    }

    #[Route("/note/delete/{slug}", name: "app_delete")]
    function deleteNote(int $slug, NoteRepository $noteRepository)
    {
        $note = $noteRepository->find($slug);
        $noteRepository->remove($note, true);
        $this->addFlash("success", "Note succesfully removed");
        return $this->redirectToRoute("app_list");
    }

    #[Route("notes/update/{slug}", name: "app_update")]
    function notesUpdate(Request $request, int $slug, NoteRepository $noteRepository, CategoryRepository $categoryRepository)
    {

        $note = $noteRepository->find($slug);
        $categories = $categoryRepository->findAll();

        $form = $this->createFormBuilder($note)->add("description")->add("idCategory", ChoiceType::class, [
            "choices" => [
                "Category 1" => $categories[0],
                "Category 2" => $categories[1],
                "Category 3" => $categories[2]
            ]
        ])->add("createdAt")->add("submit", SubmitType::class, ["label" => "Submit"])->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $new = $form->getData();
            $noteRepository->add($new, true);
            $this->addFlash("success", "Note " . $new->getId() . " updated");
            return $this->redirectToRoute("app_list");
        }

        return $this->render("form.html.twig", ["form" => $form]);

    }

}