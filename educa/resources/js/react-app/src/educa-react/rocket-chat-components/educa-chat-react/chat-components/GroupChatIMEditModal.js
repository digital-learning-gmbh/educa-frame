import React, {Component} from 'react';
import Modal from "react-bootstrap/Modal";
import {Badge, FormControl} from "react-bootstrap";
import Button from "react-bootstrap/Button";
import Select from "react-select";
import {getDisplayPair} from "../../../../shared/shared-components/Inputs";
import {connect} from "react-redux";
import {EducaDefaultTable} from "../../../../shared/shared-components/Tables";
import {RCHelper} from "../../RocketChatHelper";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";


class GroupChatIMEditModal extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                isOpen : false,
                membersCloudUsers : [],
                name : "",
            }
        this.newMembersCloudUsers = []
    }

    componentDidMount() {
        this._isMounted = true
    }

    componentWillUnmount() {
        this._isMounted = false
    }

    open(room, cloudUsers, rcUsers, roles)
    {
        let preparedCloudUsers = cloudUsers?.filter(u => this.props.store.currentCloudUser?.email !== u?.email)
        if(this._isMounted) this.setState({
            isOpen : true,
            room : room,
            name : room.name,
            membersCloudUsers : preparedCloudUsers,
        })

        this.newMembersCloudUsers = preparedCloudUsers
        this.allUsers = this.props.store.allCloudUsers?.filter( u => !!u?.rcUser?.uid && this.props.store.currentCloudUser?.email !== u.email)
            .map( u => {
                let role = roles?.find(role => role?.u?._id && role?.u?._id == u?.rcUser?.uid)
                if(role)
                    u.roles = role.roles
                return u
            })
    }

    close()
    {
        this.allUsers = []
        this.newMembersCloudUsers = []
        this.setState({isOpen : false, room : null})
    }

    save()
    {
        let usersToAdd = []
        let usersToDelete = []

        this.newMembersCloudUsers?.forEach( newU =>
        {
            if( !! this.state.membersCloudUsers.find( cu => cu.email === newU.email) )
                return
            else
                usersToAdd.push(newU)
        })

        this.state.membersCloudUsers?.forEach( cu =>
        {
            if( !! this.newMembersCloudUsers.find( newU => newU.email === cu.email) )
                return
            else
                usersToDelete.push(cu)
        })
        let promises = []


        promises = promises.concat(this.rename(this.state.name))
        promises = promises.concat(this.addUsers(usersToAdd))
        promises = promises.concat(this.removeUsers(usersToDelete))

        Promise.all(promises)
            .then( vals =>
            {
                let errors = vals?.filter( e => !!e)
                if(errors.length > 0)
                    SharedHelper.fireErrorToast("Fehler", "Der Chat konnte nicht oder nur teilweise gespeichert werden. \n"+errors.join("\n\n"))
                else
                {
                    SharedHelper.fireSuccessToast("Erfolg", "Der Chat wurde updated. ")
                }
                this.props.reloadTrigger()
                this.close()
            })

    }

    addUsers(users)
    {
        if(users?.length > 0)
            return users.map( user =>
           RCHelper.addUserToGroup(this.state.room._id, user?.rcUser?.uid)
               .then( resp => { if(resp.status !== 200) return resp.error })
            )
        return []

    }

    removeUsers(users)
    {
        console.log(users)
        if(users?.length > 0)
            return users.map( user =>
                RCHelper.kickUserFromGroup(this.state.room._id, user?.rcUser?.uid)
                .then( resp => { if(resp.status !== 200) return resp.error })
            )
        return []
    }

    rename(name)
    {
        if( name == this.state.room.name || !name)
            return []

        return RCHelper.renameGroup(this.state.room._id, name)
            .then( resp => { if(resp.status !== 200) return resp.error})
    }

    render() {

        const preselected = {}
        this.state.membersCloudUsers?.forEach(u =>
        {
            let index =  this.allUsers?.findIndex( us => us.email == u.email )
            if(index >= 0 )
              preselected[index] = true
        })

        const room = this.state.room
        if(!room)
            return null

        return <Modal
            show={this.state.isOpen}
            backdrop={"static"}
            onHide={() => {this.close()}}
        >
            <Modal.Header>
                <Modal.Title>
                    {room?.name} ändern
                </Modal.Title>
            </Modal.Header>
            <Modal.Body>
                {getDisplayPair("Neuer Name",
                    <FormControl
                        value={this.state.name}
                        onChange={(e) => this.setState({name : e.target.value})}
                        />
                )}
                {getDisplayPair("Mitglieder",
                    /*<Select
                    styles={{
                        // Fixes the overlapping problem of the component
                        menu: provided => ({...provided, zIndex: 9999}),
                    }}
                    closeMenuOnSelect={false}
                    isClearable={true}
                    getOptionLabel={(option) => option.name}
                    getOptionValue={(option) => option.email}
                    noOptionsMessage={() => "Keine Optionen"}
                    placeholder={"Mitglieder auswählen..."}
                    isMulti={true}
                    options={this.props.store.allCloudUsers}
                    value={this.state.newMembersCloudUsers}
                    onChange={(nu) => this.setState({newMembersCloudUsers : nu})}
            />*/
                <EducaDefaultTable
                    multiSelect={true}
                    preSelectedRelectedRowIds={preselected}

                    onSelectionChanged={(nu) => { this.newMembersCloudUsers =  nu }}
                    defaultPageSize={10}
                    pagination={true}
                    columns={[{Header : "Name", accessor : "name"},{Header :"Rolle", accessor: "roleComp"}]}
                    data={this.allUsers?.map( u => ({...u, roleComp : u?.roles?.map(r => <Badge className={"mr-1"} variant={"primary"}>{r}</Badge>) }))}
                />
                )}
            </Modal.Body>
            <Modal.Footer>
                <Button
                    onClick={()=> {this.save()}}
                    variant={"primary"}
                    className={"m-2"}>Speichern</Button>
                <Button
                    onClick={()=> {this.close()}}
                    variant={"danger"}>Abbrechen</Button>
            </Modal.Footer>
        </Modal>
    }
}

const mapStateToProps = state => ({store: state})

export default connect(mapStateToProps,null,null, {forwardRef : true})(GroupChatIMEditModal);
