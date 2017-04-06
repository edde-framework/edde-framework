import {IControl} from "../../Api/Control/Edde.Api.Control.IControl";

export abstract class AbstractControl implements IControl {
	abstract update(): void;
}
