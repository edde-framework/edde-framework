/**
 * This is simple library build around simple diff protocol to apply changes to DOM tree without
 * pain.
 */
Edde.MiniDiff = {
	current: null,
	Commands: {
		'add-node': function (command) {
			var root = command.path ? $(command.path).get(0) : Edde.MiniDiff.current;
			var node = Edde.MiniDiff.current = document.createElement(command.node);
			if (command.text) {
				node.appendChild(document.createTextNode(command.text));
			}
			root.appendChild(node);
		},
		'set-text': function (command) {
			var root = command.path ? $(command.path).get(0) : Edde.MiniDiff.current;
			root.innerText = command.text;
		},
		'remove-node': function (command) {
			var root = command.path ? $(command.path).get(0) : Edde.MiniDiff.current;
			root.parentNode.removeChild(root);
		},
		'add-class': function (command) {
			var root = command.path ? $(command.path).get(0) : Edde.MiniDiff.current;
			var nameList = command.class.split(' ');
			var length = nameList.length;
			for (var i = 0; i < length; i++) {
				root.classList.add(nameList[i]);
			}
		},
		'remove-class': function (command) {
			var root = command.path ? $(command.path).get(0) : Edde.MiniDiff.current;
			var nameList = command.class.split(' ');
			var length = nameList.length;
			for (var i = 0; i < length; i++) {
				root.classList.remove(nameList[i]);
			}
		}
	},
	patch: function (commandList, root) {
		var length = commandList.length;
		for (var i = 0; i < length; i++) {
			command = commandList[i];
			this['Commands'][command['command']].call(this, command);
		}
	}
};
