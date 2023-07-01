import dearpygui.dearpygui as dpg
import json
from copy import deepcopy
from reportlab.pdfgen import canvas

import glob
import os


class CharacterGenerator:
    def __init__(self, character, create_mode) -> None:
        self._imported_character = character

        self._current_character = deepcopy(self._imported_character)

        self._serial_properties = self._serialize_properties(
            self._current_character["Character"]
        )
        self._serial_properties.update(
            self._serialize_properties(self._current_character["Character"])
        )

        self._text_input_width = 200

        self._platoon_alternatives = self._current_character["Config"]["platoons"]
        self._speciality_alternatives = self._current_character["Config"][
            "specialities"
        ]
        self._gender_alternatives = self._current_character["Config"]["genders"]
        self._rank_alternatives = self._current_character["Config"]["Rank Labels"]

        self._create_mode = create_mode
        self._section_title_color = [150, 250, 150]

        self._stats = {
            "Carry Capacity": {"value": 55},
            "Combat Load": {"value": 25},
            "Psycho Limit": {"value": 4},
            "Stress Limit": {"value": 4},
            "Stunt Cap": {"value": 2},
            "Leadership Points": {"value": 1},
            "Health": {"value": 2},
            "Attribute Points": {
                "value": self._get_base_attribute_points()
                - self._get_total_attribute_cost()
            },
            "Experience Points": {
                "value": self._get_base_experience_points() - self._get_total_xp_usage()
            },
            "Psycho Points": {"value": 0},
        }

        self._serial_properties.update(self._serialize_properties(self._stats))
        self._serial_properties.update(
            self._serialize_properties(
                {"Rank": {"value": self._current_character["Player Info"]["Rank"]}}
            )
        )

    def _serialize_properties(self, character):
        properties = dict()
        for key, value in character.items():
            if "value" in character[key]:
                properties.update({key: value})
            else:
                properties.update(self._serialize_properties(character[key]))
        return properties

    def _get_base_attribute_points(self) -> int:
        return self._imported_character["Config"]["Starting AP"]

    def _get_base_experience_points(self) -> int:
        return self._imported_character["Config"]["Starting EP"]

    @staticmethod
    def _get_total_knowledge_cost(skills: dict, default_cost: list) -> int:
        """
        Calulate the xp cost for either all skills.
        """
        sum_cost = 0
        for category in skills.values():
            for knowledge in category.values():
                if "cost_table" in knowledge:
                    cost_table = knowledge["cost_table"]
                else:
                    cost_table = default_cost
                sum_cost = sum_cost + cost_table[knowledge["value"]]
        return sum_cost

    def _get_total_attribute_cost(self) -> int:
        sum_points = 0
        for attribute in self._current_character["Character"]["Attributes"]["All"][
            "Attribute"
        ].values():
            sum_points = sum_points + attribute["value"]
        return sum_points

    def _get_total_xp_usage(self) -> int:
        return (
            self._get_total_knowledge_cost(
                skills=self._current_character["Character"]["Skills"]["All"],
                default_cost=self._imported_character["Config"]["skill_cost_table"],
            )
            + self._get_total_property_cost(
                self._current_character["Character"]["Expertise"]
            )
            + self._get_total_property_cost(
                self._current_character["Character"]["Traits"]
            )
        )

    @staticmethod
    def _get_total_property_cost(properties: dict) -> int:
        """
        Calculate the total cost from boolean poperties .
        For example 'Advantages'.
        """
        sum_cost = 0

        for main_group in properties:
            if main_group != "Psychotic Disadvantages":
                for sub_group in properties[main_group]:
                    for property in properties[main_group][sub_group].values():
                        if property["value"]:
                            sum_cost = sum_cost + property["cost"]
        return sum_cost

    def _get_allowed_min_value(self, item: dict) -> int:
        min = item["value"]
        if self._create_mode:
            min = item["min"]
        return min

    def _is_check_box_change_allowed(self, item: dict) -> bool:
        """
        When editing an existing character, boxes are not allowed to be unchecked.
        """
        allowed = False
        if self._create_mode or not item["value"]:
            allowed = True
        return allowed

    def _attribute_callback(self, sender, app_data, user_data: dict):
        """
        Triggered when any slider for attribute points have changed.
        """
        self._set_value_and_display_difference(
            property_data=user_data,
            sender=sender,
            new_value=app_data,
        )
        self._update_ap_status()
        self._update_psycho_limit()
        self._update_stress_limit()
        self._update_stunt_cap()
        self._update_health_limit()
        self._update_leadership_points()
        self._update_carry_capacity()
        self._update_combat_load()
        self._check_property_disable()

    def _get_value_from_character_state(self, property_data: dict) -> int:
        """
        Helper function to get a specific value.
        """
        section = property_data["section"]
        tab_label = property_data["tab_label"]
        sub_tab_label = property_data["sub_tab_label"]
        category = property_data["category"]
        label = property_data["label"]
        return self._current_character[section][tab_label][sub_tab_label][category][
            label
        ]["value"]

    def _set_value_in_character_state(self, property_data: dict, new_value: int):
        """
        Helper function to set a specific value.
        """
        section = property_data["section"]
        tab_label = property_data["tab_label"]
        sub_tab_label = property_data["sub_tab_label"]
        category = property_data["category"]
        label = property_data["label"]

        # print(f"{section} - {tab_label} - {sub_tab_label} - {category} - {label}")
        self._current_character[section][tab_label][sub_tab_label][category][label][
            "value"
        ] = new_value

    def _set_value_and_display_difference(
        self,
        property_data: dict,
        sender: int,
        new_value: int,
    ):
        """
        Helper function that will set the associated value from the slider
        and display the difference compared to the previous saved state.
        """
        self._set_value_in_character_state(
            property_data=property_data,
            new_value=new_value,
        )
        difference = new_value - self._get_value_from_character_state(
            property_data=property_data,
        )

        if difference != 0:
            difference_string = str(difference)
            if difference > 0:
                difference_string = "+" + difference_string

            dpg.set_item_label(item=sender, label=difference_string)
        else:
            dpg.set_item_label(item=sender, label="")

    def _skills_callback(self, sender, app_data, user_data: dict):
        """
        Triggered when any slider for skill points have changed.
        """

        self._set_value_and_display_difference(
            property_data=user_data,
            sender=sender,
            new_value=app_data,
        )
        self._update_xp_status()

    def _property_callback(self, sender, app_data, user_data: dict):
        """
        Triggered when any checkbox for advantages points have changed.
        """
        self._set_value_in_character_state(
            property_data=user_data,
            new_value=app_data,
        )

        self._update_xp_status()
        self._check_property_disable()
        self._update_stress_limit()
        self._update_overview()

    def _check_property_disable(self):
        for property in self._serial_properties:
            if "requirements" in self._serial_properties[property]:
                enabled = True
                for req_name, criterium in self._serial_properties[property][
                    "requirements"
                ].items():
                    if criterium["type"] == "==":
                        if (
                            not criterium["value"]
                            == self._serial_properties[req_name]["value"]
                        ):
                            enabled = False
                    if criterium["type"] == ">=":
                        if not (
                            self._serial_properties[req_name]["value"]
                            >= criterium["value"]
                        ):
                            enabled = False
                    if criterium["type"] == "<=":
                        if not (
                            self._serial_properties[req_name]["value"]
                            <= criterium["value"]
                        ):
                            enabled = False

                if enabled:
                    dpg.configure_item(property, enabled=True)
                else:
                    dpg.configure_item(property, enabled=False)

    def _check_active_bonuses(self, target: str) -> list:
        final_bonus = []
        for property in self._serial_properties:
            if "bonus" in self._serial_properties[property]:
                if self._serial_properties[property]["value"] > 0:
                    for bonus in self._serial_properties[property]["bonus"]:
                        if bonus["target"] == target:
                            this_bonus = bonus["value"]
                            if bonus["type"] == "permanent":
                                final_bonus.append(f"{this_bonus:+g}")
                            else:
                                final_bonus.append(f"({this_bonus:+g})")
        return final_bonus

    def _player_info_callback(self, sender, app_data, user_data: dict):
        """
        Triggered when changing player info such as name, platoon etc.
        """
        state = self._current_character["Player Info"][user_data["label"]] = app_data

    def _update_psycho_limit(self):
        """
        Update the printout of current Psycho limit.
        Must be called whenever a related value have been change.
        """

        psycho_limit = self._current_character["Character"]["Attributes"]["All"][
            "Attribute"
        ]["Psyche"]["value"]
        self._stats["Psycho Limit"]["value"] = psycho_limit
        dpg.set_value(
            item="Psycho Limit",
            value=psycho_limit,
        )

    def _update_stress_limit(self):
        """
        Update the printout of current Stress limit.
        Must be called whenever a related value have been change.
        """

        stress_limit = (
            self._current_character["Character"]["Attributes"]["All"]["Attribute"][
                "Psyche"
            ]["value"]
            + 1
        )
        bonus_string = "".join(self._check_active_bonuses("Stress Limit"))
        self._stats["Stress Limit"]["value"] = stress_limit
        dpg.set_value(
            item="Stress Limit",
            value=f"{stress_limit} {bonus_string}",
        )

    def _update_stunt_cap(self):
        """
        Update the printout of current stunt cap.
        Must be called whenever a related value have been change.
        """

        stunt_cap = self._current_character["Character"]["Attributes"]["All"][
            "Attribute"
        ]["Charisma"]["value"]
        self._stats["Stunt Cap"]["value"] = stunt_cap
        dpg.set_value(
            item="Stunt Cap",
            value=stunt_cap,
        )

    def _update_health_limit(self):
        """
        Update the printout of current health.
        Must be called whenever a related value have been change.
        """

        health = (
            self._current_character["Character"]["Attributes"]["All"]["Attribute"][
                "Endurance"
            ]["value"]
            + 3
        )
        self._stats["Health"]["value"] = health
        dpg.set_value(
            item="Health",
            value=health,
        )

    def _update_carry_capacity(self):
        """
        Update the printout of current carry capacity.
        Must be called whenever a related value have been change.
        """
        carry_capacity = self._current_character["Config"]["Carry Capacity Table"][
            self._current_character["Character"]["Attributes"]["All"]["Attribute"][
                "Strength"
            ]["value"]
            - 1
        ]
        self._stats["Carry Capacity"]["value"] = carry_capacity
        dpg.set_value(
            item="Carry Capacity",
            value=carry_capacity,
        )

    def _update_combat_load(self):
        """
        Update the printout of current combat load.
        Must be called whenever a related value have been change.
        """
        combat_load = self._current_character["Config"]["Combat Load Table"][
            self._current_character["Character"]["Attributes"]["All"]["Attribute"][
                "Strength"
            ]["value"]
            - 1
        ]
        self._stats["Combat Load"]["value"] = combat_load
        dpg.set_value(
            item="Combat Load",
            value=combat_load,
        )

    def _update_leadership_points(self):
        """
        Update the printout of current health.
        Must be called whenever a related value have been change.
        """
        rank_index = self._current_character["Player Info"]["Rank"]
        leadership_points = (
            self._current_character["Config"]["Rank Bonus"][rank_index]
            + self._current_character["Character"]["Attributes"]["All"]["Attribute"][
                "Charisma"
            ]["value"]
        )
        self._stats["Attribute Points"]["value"] = leadership_points
        dpg.set_value(
            item="Leadership Points",
            value=leadership_points,
        )

    def _update_ap_status(self):
        """
        Update the printout of available attribute points.
        Must be called whenever a related value have been change.
        """
        remaining = self._get_base_attribute_points() - self._get_total_attribute_cost()
        self._stats["Attribute Points"]["value"] = remaining
        dpg.set_value(item="Attribute Points", value=remaining)

    def _update_xp_status(self):
        """
        Update the printout of available experience points.
        Must be called whenever a related value have been change.
        """
        remaining = self._get_base_experience_points() - self._get_total_xp_usage()
        self._stats["Experience Points"]["value"] = remaining
        dpg.set_value(item="Experience Points", value=remaining)

    def _update_overview(self):
        overview_list = ""
        for property_key, property_value in self._serial_properties.items():
            if "cost" in property_value and property_value["value"]:
                cost = property_value["cost"]
                overview_list = overview_list + f"{property_key} ({cost})\n"
        dpg.set_value("overview_list", overview_list)

    def _save_character_callback(self):
        file_path = os.path.abspath(
            "local_characters/"
            + self._current_character["Player Info"]["Name"].replace(" ", "_").lower()
            + ".json"
        )
        CharacterExport.to_json(file_path, self._current_character)

    def _export_to_pdf_callback(self):
        file_path = os.path.abspath(
            "local_characters/"
            + self._current_character["Player Info"]["Name"].replace(" ", "_").lower()
            + ".pdf"
        )
        ctp = CharacterToPdf(self._current_character, self._stats, file_path)
        ctp.write_pdf()
        print(f"Created: {file_path}")
        os.startfile(file_path, "open")

    @staticmethod
    def _split_dict(source, num_per_part, max_row_count=24):
        """
        Helper function to dived a set of components into groups
        in order to control the number of items shown horisontally.
        For example.
        * Limit the amount traits/advantages/disadvantages for each column.
        " Limit the amount of skill sub categories for each column.
        """
        split_items = []
        part = dict()
        row_count = 0
        category_count = 0
        for item_key, item_value in source.items():
            part[item_key] = item_value
            category_count = category_count + 1
            row_count = row_count + len(item_value)
            if (category_count >= num_per_part) or (row_count >= max_row_count):
                split_items.append(part)
                part = dict()
                category_count = 0
                row_count = 0
        if category_count > 0:
            split_items.append(part)
        return split_items

    def _add_character_setup(self):
        dpg.add_text("Character Setup", color=self._section_title_color)
        with dpg.table(
            header_row=False, policy=dpg.mvTable_SizingStretchProp, row_background=False
        ):
            dpg.add_table_column()
            dpg.add_table_column()
            with dpg.table_row():
                dpg.add_text("Player:")
                if self._create_mode:
                    dpg.add_input_text(
                        tag="player_input_text",
                        default_value=self._current_character["Player Info"]["Player"],
                        width=self._text_input_width,
                        enabled=self._create_mode,
                        user_data={"label": "Player"},
                        callback=self._player_info_callback,
                    )
                else:
                    dpg.add_text(self._current_character["Player Info"]["Player"])

            with dpg.table_row():
                dpg.add_text("E-mail:")
                if self._create_mode:
                    dpg.add_input_text(
                        tag="email_input_text",
                        default_value=self._current_character["Player Info"]["E-mail"],
                        width=self._text_input_width,
                        enabled=self._create_mode,
                        user_data={"label": "E-mail"},
                        callback=self._player_info_callback,
                    )
                else:
                    dpg.add_text(self._current_character["Player Info"]["E-mail"])

            with dpg.table_row():
                dpg.add_text("Name:")
                if self._create_mode:
                    dpg.add_input_text(
                        default_value=self._current_character["Player Info"]["Name"],
                        width=self._text_input_width,
                        enabled=self._create_mode,
                        user_data={"label": "Name"},
                        callback=self._player_info_callback,
                    )
                else:
                    dpg.add_text(self._current_character["Player Info"]["Name"])

            with dpg.table_row():
                dpg.add_text("Platoon:")
                current_platoon = self._current_character["Player Info"]["Platoon"]

                if self._create_mode:
                    dpg.add_combo(
                        items=self._platoon_alternatives,
                        width=self._text_input_width,
                        default_value=self._platoon_alternatives[0],
                        user_data={"label": "Platoon"},
                        callback=self._player_info_callback,
                    )
                else:
                    dpg.add_text(current_platoon)

            with dpg.table_row():
                dpg.add_text("Rank:")
                rank_index = self._current_character["Player Info"]["Rank"]
                rank_label = self._current_character["Config"]["Rank Labels"][
                    rank_index
                ]
                dpg.add_text(rank_label)

            with dpg.table_row():
                dpg.add_text("Speciality:")
                current_speciality = self._current_character["Player Info"][
                    "Speciality"
                ]
                if self._create_mode:
                    dpg.add_combo(
                        items=self._speciality_alternatives,
                        width=self._text_input_width,
                        default_value=current_speciality,
                        enabled=self._create_mode,
                        user_data={"label": "Speciality"},
                        callback=self._player_info_callback,
                    )
                else:
                    dpg.add_text(current_speciality)

            with dpg.table_row():
                dpg.add_text("Gender:")
                current_gender = self._current_character["Player Info"]["Gender"]

                if self._create_mode:
                    dpg.add_combo(
                        tag="gender_input_combo",
                        items=self._gender_alternatives,
                        width=self._text_input_width,
                        default_value=current_gender,
                        enabled=self._create_mode,
                        user_data={"label": "Gender"},
                        callback=self._player_info_callback,
                    )
                else:
                    dpg.add_text(current_gender)

            with dpg.table_row():
                dpg.add_text("Age:")
                current_age = self._current_character["Player Info"]["Age"]

                if self._create_mode:
                    dpg.add_slider_int(
                        tag="age_input_combo",
                        min_value=self._imported_character["Config"]["age"]["min"],
                        max_value=self._imported_character["Config"]["age"]["max"],
                        default_value=current_age,
                        enabled=self._create_mode,
                        user_data={"label": "Age"},
                        callback=self._player_info_callback,
                    )
                else:
                    dpg.add_text(current_age)

    def _add_property_check_boxes(
        self,
        section,
        tab_label,
        sub_tab_label,
        num_per_row=4,
        label_width=130,
        cost_width=25,
        show_cost=True,
        callback=None,
    ):
        """
        Add components for traits, advantages or disadvantages.
        """
        item_refs = dict()
        properties = self._current_character[section][tab_label][sub_tab_label]
        split_items = self._split_dict(properties, num_per_row)

        with dpg.group(horizontal=True):
            for part in split_items:
                with dpg.group():
                    for category_key, category_value in part.items():
                        with dpg.group(width=300):
                            dpg.add_text(category_key, color=self._section_title_color)
                            for property_key, propery_value in category_value.items():
                                with dpg.group(horizontal=True):
                                    with dpg.table(
                                        header_row=False,
                                        row_background=False,
                                        no_host_extendX=True,
                                    ):
                                        dpg.add_table_column(
                                            width_fixed=True, init_width_or_weight=20
                                        )
                                        dpg.add_table_column(
                                            width_fixed=True,
                                            init_width_or_weight=label_width,
                                        )
                                        if show_cost and "cost" in propery_value:
                                            dpg.add_table_column(
                                                width_fixed=True,
                                                init_width_or_weight=cost_width,
                                            )
                                            cost = propery_value["cost"]
                                        else:
                                            cost = None
                                        with dpg.table_row():
                                            item_id = dpg.add_checkbox(
                                                tag=property_key,
                                                user_data={
                                                    "section": section,
                                                    "tab_label": tab_label,
                                                    "sub_tab_label": sub_tab_label,
                                                    "category": category_key,
                                                    "label": property_key,
                                                },
                                                indent=5,
                                                callback=callback,
                                                default_value=bool(
                                                    propery_value["value"]
                                                ),
                                                enabled=self._is_check_box_change_allowed(
                                                    propery_value
                                                ),
                                            )
                                            item_refs[property_key] = item_id
                                            dpg.add_text(property_key)
                                            if show_cost:
                                                dpg.add_text(
                                                    f"({propery_value['cost']})"
                                                )
        return item_refs

    def _add_slider_input(
        self,
        section,
        tab_label,
        sub_tab_label,
        num_per_row=3,
        label_width=170,
        callback=None,
    ):
        """
        Add sliders for skills.
        """
        categories = self._current_character[section][tab_label][sub_tab_label]
        item_refs = dict()
        split_items = self._split_dict(categories, num_per_row)
        with dpg.group(horizontal=True):
            for part in split_items:
                with dpg.group():
                    for category_key, category_value in part.items():
                        with dpg.group(width=300):
                            dpg.add_text(category_key, color=self._section_title_color)
                            for property_key, property_value in category_value.items():
                                with dpg.table(
                                    header_row=False,
                                    row_background=False,
                                    no_host_extendX=True,
                                ):
                                    dpg.add_table_column(
                                        width_fixed=True,
                                        init_width_or_weight=label_width,
                                    )
                                    dpg.add_table_column(
                                        width_fixed=True, init_width_or_weight=100
                                    )
                                    with dpg.table_row():
                                        dpg.add_text(
                                            property_key,
                                            tag="tooltip_" + property_key,
                                            indent=5,
                                        )
                                        item_id = dpg.add_slider_int(
                                            tag=property_key,
                                            default_value=property_value["value"],
                                            min_value=self._get_allowed_min_value(
                                                property_value,
                                            ),
                                            max_value=property_value["max"],
                                            width=50,
                                            user_data={
                                                "section": section,
                                                "tab_label": tab_label,
                                                "sub_tab_label": sub_tab_label,
                                                "category": category_key,
                                                "label": property_key,
                                            },
                                            callback=callback,
                                        )
                                        item_refs[property_key] = item_id

                                        if "tooltip" in property_value:
                                            with dpg.tooltip("tooltip_" + property_key):
                                                dpg.add_text(property_value["tooltip"])
        return item_refs

    def main(self):
        with dpg.window(
            width=300,
            height=1000,
            pos=[0, 0],
            no_move=True,
            no_close=True,
            no_collapse=True,
            no_resize=True,
            no_title_bar=True,
        ):
            with dpg.group(width=300):
                self._add_character_setup()

                # Display Stats
                dpg.add_text("Stats", color=self._section_title_color)
                with dpg.table(
                    header_row=False,
                    policy=dpg.mvTable_SizingFixedFit,
                    row_background=False,
                ):
                    dpg.add_table_column()
                    dpg.add_table_column()
                    for stat_label, stat_value in self._stats.items():
                        with dpg.table_row():
                            dpg.add_text(stat_label)
                            dpg.add_text(
                                tag=stat_label, default_value=stat_value["value"]
                            )

            with dpg.group(width=300):
                dpg.add_spacer(height=50)
                dpg.add_button(
                    label="Save Character", callback=self._save_character_callback
                )
                dpg.add_button(
                    label="Export to PDF", callback=self._export_to_pdf_callback
                )

            """ Hide button for character upload until implemented
            with dpg.group(width=300):
                dpg.add_spacer(height=50)
                if self._create_mode:
                    dpg.add_button(label="Submit new character to Skynet")
                else:
                    dpg.add_button(label="Submit update to Skynet")
            """

        with dpg.window(
            width=900,
            height=1000,
            pos=[301, 0],
            no_move=True,
            no_close=True,
            no_collapse=True,
            no_resize=True,
            no_title_bar=True,
        ):
            with dpg.group(width=300):
                with dpg.tab_bar(tag="Tabs"):
                    with dpg.tab(label="Attributes"):
                        self._add_slider_input(
                            section="Character",
                            tab_label="Attributes",
                            sub_tab_label="All",
                            callback=self._attribute_callback,
                        )

                    with dpg.tab(label="Skills"):
                        self._add_slider_input(
                            section="Character",
                            tab_label="Skills",
                            sub_tab_label="All",
                            callback=self._skills_callback,
                        )

                    for tab_label in ["Traits", "Expertise"]:
                        with dpg.tab(label=tab_label):
                            with dpg.tab_bar():
                                for sub_tab_label in self._current_character[
                                    "Character"
                                ][tab_label].keys():
                                    with dpg.tab(label=sub_tab_label):
                                        self._add_property_check_boxes(
                                            section="Character",
                                            tab_label=tab_label,
                                            sub_tab_label=sub_tab_label,
                                            num_per_row=3,
                                            label_width=180,
                                            callback=self._property_callback,
                                        )
        with dpg.window(
            width=300,
            height=1000,
            pos=[1202, 0],
            no_move=True,
            no_close=True,
            no_collapse=True,
            no_resize=True,
            no_title_bar=True,
        ):
            with dpg.group(width=300):
                dpg.add_text(
                    "Traits and Experise Overview", color=self._section_title_color
                )
                dpg.add_text("", tag="overview_list", indent=5)
                self._update_overview()

        self._update_xp_status()
        self._update_psycho_limit()
        self._update_stress_limit()
        self._update_stunt_cap()
        self._update_health_limit()
        self._update_leadership_points()
        self._update_carry_capacity()
        self._update_combat_load()
        self._check_property_disable()


class CharacterSelector:
    """
    Allow the user to either create a new character or load an existing one.
    """

    def __init__(self):
        self._section_title_color = [150, 250, 150]

        self._available_characters = []

        """
        When true, the character i fully editable in the same way as creating a new character.
        When False, XP can only be spend, not removed.
        This feature will be remove/hidden in the final version.
        """
        self._create_mode = False

        self._characters_files = glob.glob("local_characters/*.json")
        for file in self._characters_files:
            path, file = os.path.split(file)
            character, ext = file.split(".")
            self._available_characters.append(character.replace("_", " ").title())

        if self._available_characters:
            self._selected_character = self._available_characters[0]
            self._selected_character_file = self._characters_files[0]
        else:
            self._selected_character = None
            self._selected_character_file = None

        # ci = CharacterImport.from_json(self._selected_character_file)
        # cg = CharacterGenerator(character=ci.get_character(), create_mode=True)
        # cg.main()

    def _character_list_callback(self, sender, app_data):
        """
        Called when selecting a character in the list.
        """
        self._selected_character = app_data

    def _edit_button_callback(self, sender, app_data):
        """
        Continue with the selected character and setup next stage for edit mode.
        """
        self._selected_character_file = self._characters_files[
            self._available_characters.index(self._selected_character)
        ]

        ci = CharacterImport.from_json(self._selected_character_file)
        cg = CharacterGenerator(
            character=ci.get_character(), create_mode=self._create_mode
        )
        cg.main()

    def _admin_button_callback(self, sender, app_data):
        """
        Update creation mode.
        """
        self._create_mode = app_data

    def _create_button_callback(self, sender, app_data):
        """
        Continue with template character and setup next stage for creation mode.
        """
        self._selected_character_file = r"local_characters/template/template.json"

        ci = CharacterImport.from_json(self._selected_character_file)
        cg = CharacterGenerator(character=ci.get_character(), create_mode=True)
        cg.main()

    def _connect_button_callback(self, sender, app_data):
        self._add_character_selection()

    def _add_login(self):
        with dpg.group(width=200):
            dpg.add_text("Credentials:", color=self._section_title_color)
            with dpg.group(horizontal=True):
                dpg.add_text("Username:", color=self._section_title_color, indent=10)
                dpg.add_input_text(default_value="skynet_user", enabled=False, width=50)
            with dpg.group(horizontal=True):
                dpg.add_text("Password:", color=self._section_title_color, indent=10)
                dpg.add_input_text(default_value="skynet_password", enabled=False)
            dpg.add_button(label="Connect", callback=self._connect_button_callback)
            dpg.add_spacer(height=20)

    def _add_character_selection(self):
        with dpg.group(width=300, parent="character_selector"):
            dpg.add_button(
                label="Create New Character", callback=self._create_button_callback
            )
            dpg.add_spacer(height=20)

            dpg.add_listbox(
                self._available_characters,
                callback=self._character_list_callback,
                num_items=10,
            )
            dpg.add_checkbox(label="Admin Mode", callback=self._admin_button_callback)
            dpg.add_button(label="Edit Character", callback=self._edit_button_callback)

    def main(self):
        """
        Set Login options.
        Select existing character or create new
        """

        # Uncomment this and comment below to skip selector page and
        # go directly to character creation.
        """
        self._selected_character_file = r"local_characters/template/template.json"

        ci = CharacterImport.from_json(self._selected_character_file)
        cg = CharacterGenerator(character=ci.get_character(), create_mode=True)
        cg.main()
        """

        with dpg.window(
            width=380,
            height=400,
            pos=[0, 0],
            tag="character_selector",
            no_move=True,
            no_close=True,
            no_collapse=True,
            no_resize=True,
            no_title_bar=True,
        ):
            # Skip login for now and go directly to the character selector
            # self._add_login()
            self._add_character_selection()


class CharacterImport:
    """
    Handle import of character. Currenty only from json-file.
    """

    def __init__(self, character):
        self._character = character

    @classmethod
    def from_json(cls, character_path):
        with open(character_path) as setup_file:
            imported_character = json.load(setup_file)

        return cls(imported_character)

    def get_character(self):
        return self._character


class CharacterExport:
    """
    Handle import of character. Currenty only from json-file.
    """

    def __init__(self, character_path, character):
        self._character = deepcopy(character)
        self._character_path = character_path

    @classmethod
    def to_json(cls, character_path, character):
        character_out = deepcopy(character)
        # Convert from true/false to 1/0
        for tab_label in character_out["Character"]:
            for sub_tab_label in character_out["Character"][tab_label].keys():
                for category in character_out["Character"][tab_label][
                    sub_tab_label
                ].keys():
                    for label in character_out["Character"][tab_label][sub_tab_label][
                        category
                    ].keys():
                        character_out["Character"][tab_label][sub_tab_label][category][
                            label
                        ]["value"] = int(
                            character_out["Character"][tab_label][sub_tab_label][
                                category
                            ][label]["value"]
                        )
        as_json = json.dumps(character_out, indent=4)
        with open(character_path, "w") as out_file:
            out_file.write(as_json)
        return cls(character_path, character)


class CharacterToPdf:
    def __init__(self, character, stats, out_file):
        self._line_height = 15
        self._current_y = 820
        self._current_x = 20
        self._font_size = 12
        self._character = character
        self._stats = stats
        self.out_file = out_file
        self._canvas = canvas.Canvas(self.out_file, pagesize=(595, 842))

    def _write_line(self, line, title=False):
        if title:
            self._canvas.setFont("Helvetica-Bold", self._font_size)
        else:
            self._canvas.setFont("Helvetica", self._font_size)
        self._canvas.drawString(self._current_x, self._current_y, line)
        self._current_y = self._current_y - self._line_height
        if self._current_y < 0:
            self._current_y = 820
            self._current_x = 200

    def write_pdf(self):
        self._write_line("Character Info", title=True)
        for key, value in self._character["Player Info"].items():
            if key == "Rank":
                rank_index = self._character["Player Info"]["Rank"]
                rank_label = self._character["Config"]["Rank Labels"][rank_index]
                self._write_line(f"{key}: {rank_label}")
            else:
                self._write_line(f"{key}: {value}")

        total_xp = self._character["Config"]["Starting EP"]
        remaing_xp = self._stats["Experience Points"]["value"]
        total_ap = self._character["Config"]["Starting AP"]
        remaing_ap = self._stats["Attribute Points"]["value"]

        self._write_line(" ")
        self._write_line(f"Total XP: {total_xp}")
        self._write_line(f"Remaining XP: {remaing_xp}")
        self._write_line(f"Total AP: {total_ap}")
        self._write_line(f"Remaining AP: {remaing_ap}")
        self._write_line(" ")

        self._write_line("Attributes", title=True)
        for attribute, content in self._character["Character"]["Attributes"]["All"][
            "Attribute"
        ].items():
            value = content["value"]
            self._write_line(f"{attribute}: {value}")

        self._write_line("Skills", title=True)
        for content in self._character["Character"]["Skills"]["All"].values():
            for skill, skill_content in content.items():
                value = skill_content["value"]
                if value > 0:
                    self._write_line(f"{skill}: {value}")

        for tab_label in ["Traits", "Expertise"]:
            self._write_line(tab_label, title=True)
            for sub_tab_label, sub_tab_content in self._character["Character"][
                tab_label
            ].items():
                self._write_line(sub_tab_label, title=True)
                for category_content in sub_tab_content.values():
                    for label, label_content in category_content.items():
                        value = label_content["value"]
                        cost = label_content["cost"]
                        if value > 0:
                            self._write_line(f"{label} ({cost})")

        self._canvas.showPage()
        self._canvas.save()


def set_theme():
    with dpg.theme() as global_theme:
        with dpg.theme_component(dpg.mvAll):
            dpg.add_theme_style(
                dpg.mvStyleVar_FrameRounding, 5, category=dpg.mvThemeCat_Core
            )

        with dpg.theme_component(dpg.mvCheckbox):
            dpg.add_theme_color(
                dpg.mvThemeCol_CheckMark, [150, 250, 150], category=dpg.mvThemeCat_Core
            )

        with dpg.theme_component(dpg.mvCheckbox, enabled_state=False):
            dpg.add_theme_color(dpg.mvThemeCol_FrameBg, [0, 0, 0])
            dpg.add_theme_color(dpg.mvThemeCol_FrameBgHovered, [0, 0, 0])
            dpg.add_theme_color(dpg.mvThemeCol_FrameBgActive, [0, 0, 0])

    dpg.bind_theme(global_theme)


if __name__ == "__main__":
    dpg.create_context()

    set_theme()

    cs = CharacterSelector()
    cs.main()

    dpg.create_viewport(title="USCM Character Editor", width=1730, height=1050)
    dpg.setup_dearpygui()

    dpg.show_viewport()
    dpg.start_dearpygui()
    dpg.destroy_context()
