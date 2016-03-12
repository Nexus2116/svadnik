<?php

namespace Admin\Service;
	
	class Tree {

		// calculate new elements node address
		public function make($node) {
			$parent = \Model\Articles::find($node->parent_id);

			$childsCount = \Model\Articles::where('parent_id', $node->parent_id)->count();

			$node->path = $node->id;
			$node->level = 0;
			$node->sort = $childsCount;
			if($parent != null) {
				$node->path = $parent->path . '.' . $node->id;
				$node->level = $parent->level + 1;
				$node->sort = $parent->sort . '.' . $childsCount;
			}

			$node->save();
			$this->updateParentNode($parent);
		}

		protected function updateParentNode($parent) {
			if($parent == null)
				return null;
			$parent->childs = count($this->childs($parent->path, $parent->level));
			$parent->save();
		}

		public function getTree() {
			$tree = \Model\Articles::where('deleted_at', null)->orderBy('sort')->get();

			$data = array ();
			foreach ($tree as $node)
				$data[$node->parent_id][$node->id] = $node;
			
			return $data;
		}

		// get child nodes
		public function childs($node, $level = null) {
			return \Model\Articles::where('path', 'LIKE', $node . '.%')->where('level', $level+1)->get();
		}

		public function subNodes($node) {
			return \Model\Articles::where('path', 'LIKE', $node . '.%')->get();
		}

		// get parent nodes
		public function parents($node) {
			return \Model\Articles::where('path', 'LIKE', '%.' . $node)->get();
		}

		// get all node
		public function node() {

		}

		// move node to another node
		public function move($id, $parent_id) {

		}

		/*
			@id - int, item id
			@sort - int, item sort order
		*/
		public function sort($node, $sort) {

			// divide sortpath by dot
			$sortPath = explode('.', $node->sort);

			// find item by index = items level and replace it by new value
			$sortPath[$node->level] = $sort;

			// join sortPath with dots
			$node->sort = join('.', $sortPath);

			// save new sort value
			$node->save();
		}

		public function delete($id) {

			$date = date('Y-m-d H:i:s');

			$article = \Model\Articles::find($id);
			$article->deleted_at = $date;
			$article->childs = 0;
			$article->save();

			// set deleted all children at this node
			\Model\Articles::where('path', 'LIKE', $article->path . '.%')->update(array (
				'deleted_at' => $date
			));

			// increment parents children count
			$parent = \Model\Articles::find($article->parent_id);
			if($parent != null) {
				$parent->childs--;
				$parent->save();
			}
		}

	}
?>