<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
Use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\ManagerRegistry as DoctrineManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\ArticleType;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Entity\CategorySearch;
use App\Form\CategorySearchType;
use App\Entity\PriceSearch;
use App\Form\PriceSearchType;




class IndexController extends AbstractController
{

      /** *@Route("/",name="article_list") */
      public function home(Request $request , ManagerRegistry $registry)
      { 
        $propertySearch = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class,$propertySearch);
        $form->handleRequest($request);
       //initialement le tableau des articles est vide, 
       //c.a.d on affiche les articles que lorsque l'utilisateur clique sur le bouton rechercher
        $articles= [];
        
       if($form->isSubmitted() && $form->isValid()) {
       //on récupère le nom d'article tapé dans le formulaire
        $nom = $propertySearch->getNom();   
        if ($nom!="") 
          //si on a fourni un nom d'article on affiche tous les articles ayant ce nom
          $articles= $registry->getRepository(Article::class)->findBy(['nom' => $nom] );
        else   
          //si si aucun nom n'est fourni on affiche tous les articles
          $articles=$registry->getRepository(Article::class)->findAll();
       }
        return  $this->render('articles/index.html.twig',[ 'form' =>$form->createView(), 'articles' => $articles]);  
      }



// /**
//  * @Route("/article/save")
//  */
// public function save(ManagerRegistry $registry) {
//     $entityManager = $registry->getManager();
//     $article = new Article();
//     $article->setNom('Article 3');
//     $article->setPrix(3500);
    
//     $entityManager->persist($article);
//     $entityManager->flush();
//     return new Response('Article enregisté avec id '.$article->getId());
//     }


    /**
 * @Route("/article/new", name="new_article")
 * Method({"GET", "POST"})
 */
 public function new(Request $request , ManagerRegistry $registry) {
    $article = new Article();
    $form = $this->createForm(ArticleType::class,$article);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
    $article = $form->getData();
    $entityManager = $registry->getManager();
    $entityManager->persist($article);
    $entityManager->flush();
    return $this->redirectToRoute('article_list');
    }
    return $this->render('article/new.html.twig',['form' => $form->createView()]);
    }

 /**
 * @Route("/article/{id}", name="article_show")
 */
public function show($id , ManagerRegistry $registry) {
    $article = $registry->getRepository(Article::class)
    ->find($id);
    return $this->render('article/show.html.twig',
    array('article' => $article));
     }



     /**
 * @Route("/article/edit/{id}", name="edit_article")
 * Method({"GET", "POST"})
 */
 public function edit(Request $request, $id , ManagerRegistry $registry) {
    $article = new Article();
   $article = $registry->getRepository(Article::class)->find($id);
   
    $form = $this->createForm(ArticleType::class,$article);
   
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
   
    $entityManager = $registry->getManager();
    $entityManager->flush();
   
    return $this->redirectToRoute('article_list');
    }
   
    return $this->render('article/edit.html.twig', ['form' =>
   $form->createView()]);
    }
   

 /**
 * @Route("/article/delete/{id}",name="delete_article")
 * @Method({"DELETE"})
*/
public function delete(Request $request, $id , ManagerRegistry $registry) {
    $article = $registry->getRepository(Article::class)->find($id);
    
    $entityManager = $registry->getManager();
    $entityManager->remove($article);
    $entityManager->flush();
    
    $response = new Response();
    $response->send();
    return $this->redirectToRoute('article_list');
    }



    /**
 * @Route("/category/newCat", name="new_category")
 * Method({"GET", "POST"})
 */
 public function newCategory(Request $request , ManagerRegistry $registry) {
    $category = new Category();
    $form = $this->createForm(CategoryType::class,$category);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
    $article = $form->getData();
    $entityManager = $registry->getManager();
    $entityManager->persist($category);
    $entityManager->flush();
    }
   return $this->render('article/newCategory.html.twig',['form'=>
   $form->createView()]);
    }



    /**
 * @Route("/art_cat/", name="article_par_cat")
 * Method({"GET", "POST"})
 */
 public function articlesParCategorie(Request $request , ManagerRegistry $registry) {
   $categorySearch = new CategorySearch();
   $form = $this->createForm(CategorySearchType::class,$categorySearch);
   $form->handleRequest($request);
   $articles= [];
   if($form->isSubmitted() && $form->isValid()) {
      $category = $categorySearch->getCategory();
      
      if ($category!="")
     $articles= $category->getArticles();
      else 
      $articles= $registry->getRepository(Article::class)->findAll();
      }
      
      return $this->render('article/articlesParCategorie.html.twig',['form' => $form->createView(),'articles' => $articles]);
      }



   /**
 * @Route("/art_prix/", name="article_par_prix")
 * Method({"GET"})
 */
 public function articlesParPrix(Request $request , ManagerRegistry $registry)
 {
 
 $priceSearch = new PriceSearch();
 $form = $this->createForm(PriceSearchType::class,$priceSearch);
 $form->handleRequest($request);
 $articles= [];
 if($form->isSubmitted() && $form->isValid()) {
 $minPrice = $priceSearch->getMinPrice();
 $maxPrice = $priceSearch->getMaxPrice();
 
 $articles= $registry->getRepository(Article::class)->findByPriceRange($minPrice,$maxPrice);
 }
 return $this->render('article/articlesParPrix.html.twig',[ 'form' =>$form->createView(), 'articles' => $articles]); 
 }


   
}
?>