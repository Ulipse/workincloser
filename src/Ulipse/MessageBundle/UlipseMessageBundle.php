<?php

namespace Ulipse\MessageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class UlipseMessageBundle extends Bundle
{
	public function getParent()
	{
		return 'FOSMessageBundle';
	}

}