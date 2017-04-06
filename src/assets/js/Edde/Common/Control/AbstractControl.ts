import {IControl} from "../../Api/Control/IControl";

export abstract class AbstractControl implements IControl {
	abstract update(): void;
}
