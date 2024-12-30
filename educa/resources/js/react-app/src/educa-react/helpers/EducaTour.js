import React, { Component, lazy, Suspense } from "react";
import { TourProvider } from '@reactour/tour'
import { Button } from "react-bootstrap";
import { connect } from "react-redux";
import {GENERAL_UPDATE_OR_ADD_GROUP} from "../reducers/GeneralReducer";

export const TUTORIAL_STEPS = [
    {
      selector: '.tenant-logo-navbar',
      content: 'Hi! Willkommen zu educa. In diesem Tutorial zeigen wir dir die wichtigsten Bedienelemente.',
    },
    {
        selector: '.apps-icon-navbar',
        content: 'Dies ist die Hauptnavigation. Hier befinden sich deine Apps auf der educa Plattform. Du kannst hier zwischen den Apps jederzeit über dieses Menü wechseln.',
    },
    {
        selector: '.searchbar-navbar',
        content: 'Du suchst etwas? Zentral in der Navigationsleiste befindet sich die Suche. Hierüber kannst du nach Personen, Ankündigungen, Terminen oder Aufgaben suchen.',
    },
    {
        selector: '.chat-navbar',
        content: 'Hier befindet sich der Chat. Du kannst mit allen Nutzern auf der Plattform Nachrichten austauschen und senden. Wenn du nicht als "online" angezeigt werden möchtest, kannst du das hier ändern.',
    },
    {
        selector: '.account-navbar',
        content: 'Hier findest du deinen Account, kannst dich von educa abmelden oder Einstellungen an deinem Profil vornehmen.',
    },
    {
        selector: '.groupSidemenu',
        content: 'Alle Inhalte und jede Kommunikation in educa findet immer innerhalb von Gruppen statt. Auf der Startseite siehst du alle Gruppen, in denen du Mitglied bist.',
    },
    {
        selector: '.personalFeed',
        content: 'Damit du trotzdem alle Inhalte direkt auf einen Blick siehst, ist hier dein persönlicher Lernfeed. Dieser Feed enthält alle Ankündigungen, Aufgaben, Termine und weitere Hinweise, die wichtig sind.',
    },
    {
        selector: '.announcementCard',
        content: 'Das ist eine Ankündigung. Ankündigungen sind Beiträge oder Posts in Gruppen und helfen in der Kommunikation. Je nachdem was der Ersteller erlaubt hat, können Ankündigungen kommentiert oder geliked werden.',
    },
    {
        selector: '.taskCard',
        content: 'Das ist eine Aufgabe. Aufgaben können helfen Abgaben oder Hausaufgaben digital abzubilden. In dieser Aufgaben-Karte siehst du direkt die Aufstellung und wichtige Informationen wie Frist und Aufgabensteller. Die Aufgaben-Karte taucht auf, wenn dir eine Aufgabe zur Bearbeitung zugeordnet wurde.',
    }
]


class EducaTourApp extends Component
{

    render() {
        return (
            <TourProvider steps={TUTORIAL_STEPS} scrollSmooth
            nextButton={({
             button,
             currentStep,
             stepsLength,
             setIsOpen,
             setCurrentStep,
             steps,
           }) => {
             const last = currentStep === stepsLength - 1
             return (
                 <Button
                   onClick={() => {
                       if (last) {
                         setIsOpen(false)
                         setCurrentStep(0)
                       } else {
                         setCurrentStep((s) => (s === steps?.length - 1 ? 0 : s + 1))
                       }
                   }}
                 >
                   {last ? 'Fertig 🎉' : "Weiter >"}
                 </Button>
             )
           }}
      >
                {this.props.children}
            </TourProvider>
        )
    }
}

const mapStateToProps = state => ({ store: state });

const mapDispatchToProps = dispatch => {
    return {
        // dispatching plain actions
        updateOrAddOneGroup: group =>
            dispatch({ type: GENERAL_UPDATE_OR_ADD_GROUP, payload: group })
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(EducaTourApp);
