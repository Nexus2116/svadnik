<?php

namespace Service;
	
	class Tree {

		// get parent nodes
		public function parents($node) {
			return \Model\Articles::where('path', 'LIKE', '%.' . $node)->get();
		}

		// get child nodes
		public function childs($node, $level = null) {
			return \Model\Articles::where('path', 'LIKE', $node . '.%')->where('level', $level+1)->get();
		}

	}
?>