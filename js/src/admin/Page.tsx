import ExtensionPage from "flarum/admin/components/ExtensionPage";
import app from 'flarum/admin/app';
import Button from "flarum/common/components/Button";
import LoadingIndicator from "flarum/common/components/LoadingIndicator";
import Model from "flarum/common/Model";
import ItemList from "flarum/common/utils/ItemList";
import Stream from "mithril/stream";
import Select from "flarum/common/components/Select";
function _trans(key: string, args?: Record<string, any>) {
    return app.translator.trans(`xypp-limited-request.admin.${key}`, args);
}

declare type ITEM = {
    mode: string,
    group: string,
    path: string,
    params: string[],
    group_id: number,
    method: string,
}
export default class Page extends ExtensionPage {
    existingItems: ITEM[] = [];
    dataSettings?: Stream<string>;
    modeOpt = {
        "regex": _trans("match_mode.regex"),
        "prefix": _trans("match_mode.prefix"),
        "full": _trans("match_mode.full"),
    }
    groupOpt = {
        "api": _trans("group.api"),
        "forum": _trans("group.forum"),
    }
    methodOpt = {
        "GET": _trans("match_method.GET"),
        "POST": _trans("match_method.POST"),
        "PUT": _trans("match_method.PUT"),
        "DELETE": _trans("match_method.DELETE"),
        "PATCH": _trans("match_method.PATCH"),
        "OPTIONS": _trans("match_method.OPTIONS"),
        "HEAD": _trans("match_method.HEAD"),
    }
    oninit(vnode: any): void {
        super.oninit(vnode);
        this.dataSettings = this.setting("xypp.request_limit");
        this.existingItems = JSON.parse(this.dataSettings?.() || "[]");
    }
    oncreate(vnode: any): void {
        super.oncreate(vnode);
    }
    content(vnode: any) {
        return <div className="container">
            <table className="Table">
                <thead>
                    <tr>
                        <th>{_trans("mode")}</th>
                        <th>{_trans("method")}</th>
                        <th>{_trans("route_group")}</th>
                        <th>{_trans("value")}</th>
                        <th>{_trans("params")}</th>
                        <th>{_trans("group_id")}</th>
                        <th>{_trans("action")}</th>
                    </tr>
                </thead>
                <tbody>
                    {this.table().toArray()}
                </tbody>
            </table>
            {this.submitButton()}
        </div>
    }
    table() {
        const items = new ItemList();

        this.existingItems.forEach((item, index) => {
            items.add(`item-${index}`,
                <tr key={index}>
                    <td>
                        <Select className="FormControl" options={this.modeOpt} value={item.mode} onchange={(v: string) => this.modify(index, "mode", v)} />
                    </td>
                    <td>
                        <Select className="FormControl" options={this.methodOpt} value={item.method} onchange={(v: string) => this.modify(index, "mode", v)} />
                    </td>
                    <td>
                        <Select className="FormControl" options={this.groupOpt} value={item.group} onchange={(v: string) => this.modify(index, "group", v)} />
                    </td>
                    <td>
                        <input className="FormControl" value={item.path} oninput={(e: any) => this.modify(index, "path", e.target.value)} />
                    </td>
                    <td>
                        <input className="FormControl" value={item.params.join(",")} oninput={(e: any) => this.modify(index, "params", e.target.value.split(","))} />
                    </td>
                    <td>
                        <input className="FormControl" value={item.group_id} oninput={(e: any) => this.modify(index, "group_id", e.target.value)} />
                    </td>
                    <td>
                        <Button className="Button Button--danger" onclick={() => {
                            this.existingItems.splice(index, 1);
                        }}>{_trans("delete")}</Button>
                    </td>
                </tr >)
        })

        items.add("add", <tr key="add">
            <td colSpan={5}>
                <Button className="Button Button--primary" onclick={() => {
                    this.existingItems.push({
                        mode: "full",
                        group: "api",
                        path: "",
                        method: "GET",
                        params: [],
                        group_id: 0
                    })
                }}>{_trans("add")}</Button>
            </td>

        </tr>
        )
        return items;
    }
    modify(index: number, key: keyof ITEM, value: any) {
        (this.existingItems[index] as any)[key] = value;
        this.dataSettings?.(JSON.stringify(this.existingItems));
    }
}