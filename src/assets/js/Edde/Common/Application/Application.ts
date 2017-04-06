import {IApplication} from "../../Api/Application/IApplication";
import {CheckboxControl} from "../Control/CheckboxControl";

export class Application implements IApplication {
	run() {
		let checkbox = new CheckboxControl();
		checkbox.update();
	}
}
