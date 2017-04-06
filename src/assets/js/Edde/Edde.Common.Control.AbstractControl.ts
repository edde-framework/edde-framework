import {IControl} from "./Edde.Api.Control.IControl";

export abstract class AbstractControl implements IControl {
	abstract update(): void;
}
