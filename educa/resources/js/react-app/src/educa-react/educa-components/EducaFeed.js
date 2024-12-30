import React, {Component, forwardRef} from 'react'
import {
    AppointmentCard,
    DocumentCard,
    EducaCardBlack,
    EducaCardBlue,
    EducaCardGreen, EducaCardRed,
    EducaCardWhite,
    EducaFailureCard,
    EducaNoContentCard, EducaSupportCard,
    GroupCard,
    TaskCard,
    TaskRatedCard, TaskResetCard,
    TaskSubmittedCard
} from "./EducaFeedCards/FeedCards";
import AnnouncementCard from "./EducaFeedCards/AnnouncementCard";
import {EducaShimmer} from "../../shared/shared-components/EducaShimmer";
import {connect} from "react-redux";
import {Fade} from "react-bootstrap";
import {EducaCircularButton} from "../../shared/shared-components/Buttons";
import EducaHelper from "../helpers/EducaHelper";
import FliesentischZentralrat from "../FliesentischZentralrat";
import EducaFeedStatisticsModal from "./EducaFeedStatisticsModal";
import {withRouter} from "react-router";

const FEED_RELOAD_TIME = 30000

class EducaFeed extends Component {

    constructor(props) {
        super(props);

        this.handleScroll = this.handleScroll.bind(this);
        this.state =
            {
                isLoading: true,
                feedData: [],
                key: this.props.key, // on change, re init component
                hasError: false,
                scrollTop: 0,
                newAvailableFeeds: [],
                extendLoadingInProgress: false,
                lastTimestamp: "-1",
                selectedFeedActivity : null
            }
        this.loadFeed = this.loadFeed.bind(this)
        this.refreshFeed = this.refreshFeed.bind(this)
    }

    // Check for important prop changes e.g.
    componentDidUpdate(prevProps, prevState, snapshot) {
        if (this.props.key !== this.state.key)  // prevProps.feedGetterFunc always mirrors state.feedGetterFunc
        {
            this.reInitComponent()
        }

    }

    componentDidMount() {
        this._isMounted = true
        window.addEventListener('scroll', this.handleScroll);
        this.loadFeed()

        this.timeoutFunc = this.startReloadTimer()
    }

    startReloadTimer() {
        return setTimeout(() => {
            this.loadFeed(true)
        }, FEED_RELOAD_TIME)
    }

    componentDidCatch(error, errorInfo) {
       this.setState({ hasError: true, lastErrorInfo : errorInfo });
    }

    componentWillUnmount() {
        this._isMounted = false
        window.removeEventListener('scroll', this.handleScroll);
        clearTimeout(this.timeoutFunc)
    }

    // fresh restart w/ state flush
    reInitComponent() {
        this.setState(
            {
                feedData: [],
                key: this.props.key, // on change, re init component
                hasError: false,
                scrollTop: 0,
                extendLoadingInProgress: false,
                lastTimestamp: "-1"
            })
        this.loadFeed()
    }

    refreshFeed() {
        this.setState(
            {
                isLoading: true,
                feedData: [],
                key: this.props.key, // on change, re init component
                hasError: false,
                scrollTop: 0,
                extendLoadingInProgress: false,
                lastTimestamp: "-1"
            },() => {
                this.loadFeed()
            })
    }

    loadFeed(addToAvailable) {
        if (this.props.feedGetterFunc && this._isMounted) {
            this.props.feedGetterFunc(addToAvailable ? -1 : this.state.lastTimestamp)
                .then(resp => {
                    if (this._isMounted) this.setState({extendLoadingInProgress: false});
                    if (resp.payload && resp.payload.feedData) {
                        if (this._isMounted) {
                            if (addToAvailable) {
                                let newFeeds = []
                                // iterate over the new feed data
                                resp.payload.feedData.forEach((feed, index) => {
                                    let conditionalFeed = this.state.feedData.find(f => f.id == feed.id)
                                    //if the feed is not found in the current feed
                                    if (!conditionalFeed) {
                                        newFeeds.push(feed)
                                    }
                                })
                                this.setState({newAvailableFeeds: newFeeds})
                            } else
                                this.setState({
                                    isLoading: false,
                                    hasError: false,
                                    feedData: this.state.feedData.concat(resp.payload.feedData),
                                    lastTimestamp: resp.payload.lastTimestamp
                                })

                        }

                    } else {
                        if (this._isMounted)
                            this.setState({isLoading: false, hasError: true, lastTimestamp: "-1"})
                        throw new Error("Server Error")
                    }

                    if (addToAvailable && this._isMounted) // call itself again
                        this.startReloadTimer()
                    //Loading emulation
                })
                .catch(err => {
                    EducaHelper.fireErrorToast("Fehler", "Kritischer Fehler beim Laden des Feeds. " + err.message)
                })
        }
    }

    extendFeed() {
        if (this.state.extendLoadingInProgress)
            return;
        if (this.state.lastTimestamp == null)
            return;
        if (this._isMounted) this.setState({extendLoadingInProgress: true});
        this.loadFeed();
    }

    handleScroll(event) {
        let scrollTop = event.srcElement.body.scrollTop / window.document.body.scrollHeight
        if (scrollTop > 0.8)
            this.extendFeed();
    }

    _insertUpdatedAnnouncement(newAnnouncement) {
        if (Array.isArray(this.state.feedData))
            for (let i = 0; i < this.state.feedData.length; i++) {
                if (this.state.feedData[i].payload && this.state.feedData[i].payload.announcement && this.state.feedData[i].payload.announcement.id === newAnnouncement.id) {
                    let newFeedState = this.state.feedData;
                    newFeedState[i].payload.announcement = newAnnouncement
                    if (this._isMounted) this.setState({feedData: newFeedState})
                }
            }
    }

    _deleteAnnouncement(id) {
        if (Array.isArray(this.state.feedData))
            for (let i = 0; i < this.state.feedData.length; i++) {
                if (this.state.feedData[i].payload && this.state.feedData[i].payload.announcement && this.state.feedData[i].payload.announcement.id === id) {
                    let newFeedState = this.state.feedData;
                    newFeedState.splice(i, 1);
                    if (this._isMounted) this.setState({feedData: newFeedState})
                }
            }
    }

    changeRoute(path, search) {
        this.props.history.push({
            pathname: path,
            search: search
        })
    }


    render() {
        if (this.state.isLoading) {
            return <div><EducaShimmer/>
                <EducaShimmer/>
                <EducaShimmer/></div>
        } else if (this.state.hasError) {
            return <EducaFailureCard/>;
        } else if (this.state.feedData.length === 0) {
            return <EducaNoContentCard/>;
        }
        return (
            <div>
                <Fade
                    in={!!this.state.newAvailableFeeds.length}>
                    <EducaCircularButton
                        variant={"success"}
                        onClick={() => {
                            this.setState({
                                feedData: this.state.newAvailableFeeds.concat(this.state.feedData),
                                newAvailableFeeds: []
                            })
                        }}
                        size={"big"}
                        style={this.props.reloadButtonStyle}
                    >+ {this.state.newAvailableFeeds.length}</EducaCircularButton>
                </Fade>
                {this.state.feedData.map( (obj,i) => <div key={i}>{this.parseCard(obj,i)}</div>)}
                {this.canLoadMore()}
                <EducaFeedStatisticsModal feedActivity={this.state.selectedFeedActivity} hide={() => this.setState({selectedFeedActivity : null})}/>
            </div>
        );
    }

    parseCard(feedObj) {

        const getCard = () => {
            if (feedObj.type === "coloredCard") {
                var configuration = feedObj.payload;
                if (configuration.color === "green") {
                    return <EducaCardGreen {...configuration} key={feedObj.id}/>;
                }
                if (configuration.color == "blue") {
                    return <EducaCardBlue {...configuration} key={feedObj.id}/>;
                }
                if (configuration.color == "red") {
                    return <EducaCardRed {...configuration} key={feedObj.id}/>;
                }
                if (configuration.color == "white") {
                    return <EducaCardWhite {...configuration} key={feedObj.id}/>;
                }
                return <EducaCardBlack {...configuration} key={feedObj.id}/>;
            } else if (feedObj.type === "announcementCard") {
                let configuration = feedObj.payload;
                return <AnnouncementCard
                    {...configuration}
                    canLike={FliesentischZentralrat.sectionAnnouncementLike(null, feedObj.payload?.section?.id)}
                    canComment={FliesentischZentralrat.sectionAnnouncementComment(null, feedObj.payload?.section?.id)}
                    key={"announcementCard_" + feedObj.id}
                    changeRouteCallback={(path, route) => this.changeRoute(path, route)}
                    deletedAnnouncementCallback={(id) => this._deleteAnnouncement(id)}
                    updatedAnnouncementCallback={(newAnnouncement) => this._insertUpdatedAnnouncement(newAnnouncement)}
                />;
            } else if (feedObj.type === "appointmentCard") {
                return <AppointmentCard
                    changeRouteCallback={(path, route) => this.changeRoute(path, route)}
                    key={"appointmentCard_" + feedObj.id} {...feedObj.payload} />;
            } else if (feedObj.type === "taskCard") {
                return <TaskCard
                    changeRouteCallback={(path, route) => this.changeRoute(path, route)}
                    key={"taskCard_" + feedObj.id} {...feedObj.payload} />;
            } else if (feedObj.type === "taskRatedCard") {
                return <TaskRatedCard
                    changeRouteCallback={(path, route) => this.changeRoute(path, route)}
                    key={"taskRatedCard_" + feedObj.id} {...feedObj.payload} />;
            } else if (feedObj.type === "taskResetCard") {
                return <TaskResetCard
                    changeRouteCallback={(path, route) => this.changeRoute(path, route)}
                    key={"taskRatedCard_" + feedObj.id} {...feedObj.payload} />;
            } else if (feedObj.type === "taskSubmittedCard") {
                return <TaskSubmittedCard
                    changeRouteCallback={(path, route) => this.changeRoute(path, route)}
                    key={"taskSubmittedCard_" + feedObj.id} {...feedObj.payload} />;
            } else if (feedObj.type === "groupCard") {
                return <GroupCard
                    changeRouteCallback={(path, route) => this.changeRoute(path, route)}
                    key={"groupCard_" + feedObj.id} {...feedObj.payload} />;
            } else if (feedObj.type === "documentCard") {
                return <DocumentCard
                    changeRouteCallback={(path, route) => this.changeRoute(path, route)}
                    key={"documentCard_" + feedObj.id} {...feedObj.payload} />;
            } else if (feedObj.type === "supportCard") {
                return <EducaSupportCard
                    changeRouteCallback={(path, route) => this.changeRoute(path, route)}
                    key={"supportCard_" + feedObj.id} {...feedObj.payload} />;
            }
            return <div key={"unsupported" + feedObj.id}></div>;
        }

        let card = getCard()
        if(FliesentischZentralrat.globalFeedStatistics() && this.props.showStatistics)
            return <div>
                {card}
                <div className={"float-right"} style={{cursor:"pointer"}} onClick={() => {this.setState({selectedFeedActivity : feedObj})}}>
                    <i className="far fa-chart-bar mr-1"></i>
                    Auswertungen
                </div>
                <div className={"clearfix"}></div>
            </div>

        return card
    }

    canLoadMore() {
        if (this.state.lastTimestamp == null || this.state.feedData.length < 10) {
            return (<div></div>)
        } else {

            return <div><EducaShimmer/>
                <EducaShimmer/>
                <EducaShimmer/></div>
        }
    }
}


const mapStateToProps = state => ({store: state})

const withRouterForwardRef = Component => {
    const WithRouter = withRouter(({ forwardedRef, ...props }) => (
        <Component ref={forwardedRef} {...props} />
    ));

    return forwardRef((props, ref) => (
        <WithRouter {...props} forwardedRef={ref} />
    ));
};
export default connect(mapStateToProps, null,null,{forwardRef : true})(withRouterForwardRef(EducaFeed));
