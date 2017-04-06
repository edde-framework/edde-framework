import {CheckboxControl} from "../Control/Edde.Common.Control.CheckboxControl";
import {IApplication} from "../../Api/Edde.Api";

export class Application implements IApplication {
	run(): void {
		let checkbox = new CheckboxControl();
		checkbox.update();
		console.log('foobar');
	}
}
