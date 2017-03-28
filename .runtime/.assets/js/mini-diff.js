/**
 * This is simple library build around simple diff protocol to apply changes to DOM tree without
 * pain.
 */
var MiniDiff = {
	Commands: {
		'current': null,
		'add-node': function (command) {
			var root = command.path ? Sizzle(command.path)[0] : this.current;
			var node = this.current = document.createElement(command.node);
			if (command.text) {
				node.appendChild(document.createTextNode(command.text));
			}
			root.appendChild(node);
		},
		'set-text': function (command) {
			var root = command.path ? Sizzle(command.path)[0] : this.current;
			root.innerText = command.text;
		},
		'remove-node': function (command) {
			var root = command.path ? Sizzle(command.path)[0] : this.current;
			root.parentNode.removeChild(root);
		},
		'add-class': function (command) {
			var root = command.path ? Sizzle(command.path)[0] : this.current;
			var nameList = command.class.split(' ');
			var length = nameList.length;
			for (var i = 0; i < length; i++) {
				root.classList.add(nameList[i]);
			}
		},
		'remove-class': function (command) {
			var root = command.path ? Sizzle(command.path)[0] : this.current;
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
