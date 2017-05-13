<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;
use AppBundle\Entity\Micropost;

class MicropostController extends Controller {

  /**
   * @Route("/micropost/delete/{id}", name="micropost_delete")
   */
  public function deleteAction ($id) {
    $em = $this->getDoctrine()->getManager();
    $micropost = $em->getRepository('AppBundle:Micropost')->find($id);

    if (is_null($micropost)) {
        throw $this->createNotFoundException(
            "micropost $id does not exist"
        );
    }

    $micropost_user_id = $micropost->getUser()->getId();
    $user = $this->get('security.token_storage')->getToken()->getUser();
    $user_id = $user->getId();

    if ($user->getRoles()[0] !== 'ROLE_ADMIN' &&
    		$micropost_user_id !== $user_id) {
    	$this->addFlash('notice', 'You can\'t delete that micropost!');
    } else {
    	$em->remove($micropost);
	    $em->flush();

	    $this->addFlash('notice', 'micropost deleted');
    }

    return $this->redirectToRoute('user_show', array('id' => $user_id));
  }

}